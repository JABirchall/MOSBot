<?php

namespace App;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class Bot
{
    public $client;

    private $user;

    private $config;

    public function __construct()
    {
        $this->config = json_decode(file_get_contents("config.json"), false);
        $headers = ['Authorization' => $this->config->authorization,
                    "Referer" => "https://sywoia81cjcorq88587bbrdn2t87sq.ext-twitch.tv/sywoia81cjcorq88587bbrdn2t87sq/0.0.4/cb4f1507e5df6025486f4c38910bf2d8/viewer.html?anchor=component&language=en&mode=viewer&state=released&platform=web",
                    "Origin" => "https://sywoia81cjcorq88587bbrdn2t87sq.ext-twitch.tv",
                    "User-Agent" => "MOSBot v1.0 PHP/".phpversion(),
        ];
        $this->client = new Client([
            'base_uri' => 'https://www.pbpmosext.pixelbypixelcanada.com/',
            'timeout'  => 1.0,
            'headers'  => $headers,
            'verify' => false,
        ]);

        $this->user = new User;
        $this->user->subscriber = false;
    }


    public function getGameStatus($channel)
    {
        printf("[INFO] Fetching game status for: %d - ", $channel);
        $response = $this->client->get('gamestates/' . $channel);

        $body = json_decode($response->getBody(), false);

        if($body->joinable !== true) {
            printf("Not joinable - Reason: %s\n", $body->matchState);
            return $body->joinable;
        }
        printf("Joinable\n");

        if($body->nonSubSkins > 0) {
            $this->user->skin = mt_rand(1, $body->nonSubSkins);
        }
        return $body->joinable;
    }

    public function joinRace($channel)
    {
        printf("[INFO] Attempting to join race: %d with skin %d\n", $channel, $this->user->skin);

        for ($i = 0; $i <= $this->config->usernames->limit; $i++) {
            $this->getUsername($i);

            printf("Joining with username %s\n", $this->user->username);
            $this->client->post('gamestates/join/' . $channel, [
                'json' => [
                    'username' => $this->user->username,
                    'displayname' => $this->user->displayname,
                    'skin' => $this->user->skin,
                    'subscriber' => $this->user->subscriber,
                    'emote' => 0
                ]
            ]);
        }


        return true;
    }

    private function getUsername($i)
    {
        if($this->config->usernames->random){
            $this->user->displayname = uniqid();
            $this->user->username = strtolower($this->user->displayname);
            return;
        }

        $this->user->displayname = ($this->config->usernames->list[$i] . ($i == 0 ? '' : sprintf("%'03s",$i)));
        $this->user->username = strtolower($this->user->displayname);
        return;
    }

    public function loop()
    {
        printf("[INFO] Bot starting loop over %d channels\n", count($this->config->channels));
        for($i = 0; $i <= count($this->config->channels); $i++)
        {
            if($i == count($this->config->channels)){
                $i = 0;
                printf("%'-30s\n[INFO] Bot looped over %d channels pausing for 30 seconds\n%'-30s\n",'-', count($this->config->channels),'-');
                sleep(30);
            }

            try {
                if ($this->getGameStatus($this->config->channels[$i]) !== true) {
                    continue;
                }
                $this->joinRace($this->config->channels[$i]);
            } catch(RequestException $e) {
                switch ($e->getCode()) {
                    case 401:
                        exit($e->getMessage()."\nUnauthorized, Change your token.");
                    case 404:
                        printf("Not Joinable - Reason: NotRunning\n");
                        break;
                    case 500:
                        printf("[ERROR] PixelByPixel API encounted an error - Possibly updated\n");
                        break;
                    default:
                        printf("[ERROR] Status code not OK - Code: %d\n", $e->getCode());
                }
            }
        }
    }
}