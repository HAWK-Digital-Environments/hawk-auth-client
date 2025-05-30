<?php
declare(strict_types=1);


namespace Hawk\AuthClient\Keycloak\Query;


use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;
use Hawk\AuthClient\Exception\FailedRequestDueToMissingPermissionsException;
use Hawk\AuthClient\Exception\ProfileUpdateDataInvalidException;
use Hawk\AuthClient\Exception\ProfileUpdateFailedException;
use Hawk\AuthClient\Profiles\Value\ProfileFieldValidationError;
use Hawk\AuthClient\Profiles\Value\UserProfile;
use Hawk\AuthClient\Users\Value\User;

class UpdateProfileDataQuery
{
    protected const array ALLOWED_KEYS = [
        'id', 'attributes', 'email', 'firstName', 'lastName', 'username', 'userProfileMetadata', 'emailVerified'
    ];

    protected User $user;
    protected array $changeSet;
    protected bool $asAdmin;

    public function __construct(
        User        $user,
        array $changeSet,
        bool  $asAdmin
    )
    {
        $this->user = $user;
        $this->changeSet = $changeSet;
        $this->asAdmin = $asAdmin;
    }

    public function execute(ClientInterface $client): void
    {
        try {
            $client->request(
                'PATCH',
                'realms/{realm}/hawk/profile/' . $this->user->getId(),
                [
                    'query' => [
                        'mode' => $this->asAdmin ? 'admin' : 'user'
                    ],
                    'json' => $this->buildPayload()
                ]
            );
        } catch (ClientException|RequestException $e) {
            if ($e->getResponse()->getStatusCode() === 401) {
                throw new FailedRequestDueToMissingPermissionsException(
                    'hawk-manage-profile-data',
                    previous: $e
                );
            }

            $content = $e->getResponse()->getBody()->getContents();
            $error = 'Unknown error';
            if (json_validate($content)) {
                $data = json_decode($content, true);
                if (isset($data['field'])) {
                    $error = 'Error while updating field ' . $data['field'];

                    if (isset($data['errors'])) {
                        throw new ProfileUpdateDataInvalidException(
                            $error,
                            $e,
                            ...array_map(
                                static fn(array $error) => new ProfileFieldValidationError(
                                    $error['field'],
                                    $error['errorMessage'],
                                    $error['params'] ?? []
                                ),
                                $data['errors']
                            )
                        );
                    }
                }
            }

            throw new ProfileUpdateFailedException($error, $e);
        }
    }

    protected function buildPayload(): array
    {
        $payload = [];
        $attributeChanges = $this->changeSet;
        foreach (UserProfile::ROOT_LEVEL_ATTRIBUTES as $field) {
            if (array_key_exists($field, $attributeChanges)) {
                $payload[$field] = $attributeChanges[$field];
                unset($attributeChanges[$field]);
            }
        }

        if (!empty($attributeChanges)) {
            $payload['attributes'] = $attributeChanges;
        }

        return array_filter(
            $payload,
            static fn(string $key) => in_array($key, static::ALLOWED_KEYS, true),
            ARRAY_FILTER_USE_KEY
        );
    }
}
