<?php


namespace AlexisRiot\Yousign;

class YousignProcedure
{
    private $datas;

    private $members = [];

    private $request;

    public function __construct()
    {
        $this->datas = (object) [
            "name" => "",
            "description" => "",
            "members" => [],
            "config" => [
                "webhook" => [
                    "procedure.finished" => [
                        [
                            "url" => null,
                            "method" => "GET",
                        ]
                    ]
                ]
            ],
        ];
    }

    public function withName($name) {
        $this->datas->name = $name;
        return $this;
    }

    public function withDescription($description) {
        $this->datas->description = $description;
        return $this;
    }

    public function withWebhook($url) {
        $this->datas->config['webhook']['procedure.finished'][0]['url'] = $url;
        return $this;
    }

    public function addMember($user, $files) {
        $user = (object) $user;

        array_push($this->datas->members, [
            "operationLevel" => "custom",
            "operationCustomModes" => [$user->operationCustomModes],
            "operationModeSmsConfig" => [
                "content" => "SIGNATURE - {{code}} est votre code de sécurité pour signer le document.",
            ],
            "operationModeEmailConfig" => [
                "subject" => "Your security code",
                "content" => "SIGNATURE - {{code}} est votre code de sécurité pour signer le document.",
            ],
            "firstname" => $user->firstname,
            "lastname" => $user->lastname,
            "email" => $user->email,
            "phone" => $user->phone,
            'fileObjects' => $this->loopFiles($files),
        ]);

        return $this;
    }

    private function loopFiles($files) {
        $datas = [];

        if (isset($files['id'])) {
            array_push($datas, $this->pushFile($files));
        } else {
            foreach ($files as $file) {
                array_push($datas, $this->pushFile($file));
            }
        }

        return $datas;
    }

    private function pushFile($file) {
        return [
            "file" => $file['id'],
            "page" => $file['page'] ?? 1,
            "position" => is_string($file['position']) ? $file['position'] : "341,705,556,754",
            "mention" => $file['mention'] ?? "",
            "mention2" => $file['mention2'] ?? "",
        ];
    }

    public function create() {
        return $this->request = \AlexisRiot\Yousign\Facades\Yousign::createBasicProcedure((array) $this->datas);
    }

    public function getMember(): object {
        return count($this->request['members']) === 1
            ? (object) $this->request['members'][0]
            : (object) $this->request['members'];
    }

    public function request() {
        return $this->request;
    }
}