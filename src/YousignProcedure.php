<?php

namespace AlexisRiot\Yousign;

class YousignProcedure
{
    private $data;

    private $request;

    /**
     * Instantiate a new procedure instance.
     *
     * @param  string $name
     * @param  string $description
     * @return void
     */
    public function __construct(string $name = '', string $description = '')
    {
        $this->data = (object) [
            'name' => $name,
            'description' => $description,
            'members' => [],
            'config' => [],
        ];
    }

    /**
     * Add a name to the procedure.
     *
     * @param  string $name
     * @return self
     */
    public function withName($name)
    {
        $this->data->name = $name;

        return $this;
    }

    /**
     * Add a description to the procedure.
     *
     * @param  string $description
     * @return self
     */
    public function withDescription($description)
    {
        $this->data->description = $description;

        return $this;
    }

    /**
     * Add webhooks to the procedure.
     *
     * @param  mixed $hooks
     * @param  string $url
     * @return self
     */
    public function withWebhook(mixed $hooks, string $url)
    {
        if (! is_array($hooks)) {
            $hooks = [$hooks];
        }

        foreach ($hooks as $hook) {
            $this->data->config['webhook'][$hook][] = [
                'url' => $url,
                'method' => 'GET',
            ];
        }

        return $this;
    }

    /**
     * Add a member to the procedure.
     *
     * @param  array $user
     * @param  array $files
     * @return self
     */
    public function addMember($user, $files)
    {
        $user = (object) $user;

        array_push($this->data->members, [
            'operationLevel' => 'custom',
            'operationCustomModes' => [
                $user->operationCustomModes ?: 'sms',
            ],
            'operationModeSmsConfig' => [
                'content' => trans('yousign::yousign.sms_security_code'),
            ],
            'operationModeEmailConfig' => [
                'subject' => trans('yousign::yousign.email_security_code.subject'),
                'content' => trans('yousign::yousign.email_security_code.content'),
            ],
            'firstname' => $user->firstname,
            'lastname' => $user->lastname,
            'email' => $user->email,
            'phone' => $user->phone,
            'fileObjects' => $this->getFileObjects($files),
        ]);

        return $this;
    }

    /**
     * Extract fileObjects from file array.
     *
     * @param  array $files
     * @return array
     */
    private function getFileObjects($files)
    {
        $data = [];

        if (isset($files['id'])) {
            $files = [$files];
        }

        foreach ($files as $file) {
            $fileObjects = [
                'file' => $file['id'],
                'page' => $file['options']['page'] ?? 1,
                'position' => $file['options']['position'] ?? '341,705,556,754',
                'mention' => $file['options']['mention'] ?? '',
                'mention2' => $file['options']['mention2'] ?? '',
            ];

            $data[] = $fileObjects;
        }

        return $data;
    }

    /**
     * Create a basic procedure
     *
     * @return void
     */
    public function create()
    {
        return $this->request = \AlexisRiot\Yousign\Facades\Yousign::createBasicProcedure((array) $this->data);
    }

    /**
     * Get the members for the procedure.
     *
     * @return array
     */
    public function getMembers()
    {
        return $this->request['members'];
    }
}
