<?php


namespace AlexisRiot\Yousign;

class YousignProcedure
{
    private $datas;

    private $members = [];

    public function __construct()
    {
        $this->datas = (object) [
            "name" => "",
            "description" => "",
            "members" => [],
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

    public function addMember($user, $files) {
        $user = (object) $user;

        array_push($this->datas->members, [
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
            "position" => $file['position'] ?? "",
            "mention" => $file['mention'] ?? "",
            "mention2" => $file['mention2'] ?? "",
        ];
    }

    public function send() {
        return \AlexisRiot\Yousign\Facades\Yousign::createBasicProcedure((array) $this->datas);
    }
}