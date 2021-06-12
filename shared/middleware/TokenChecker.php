<?php

namespace Middleware;

/**
 * Description of TokenChecker
 *
 * @author Thomasino
 */
class TokenChecker extends \Core\Middleware {

    public function run() {

        // setting of the default format
        $this->app->getResponse()->setFormat('json');
        $uri = $this->app->getContainer()->getRequest()->getUri();

        // display of route api Routes case urls matches
        if (strpos($uri, '/api/routes') !== FALSE) {
            $this->displayApiRoutes();
        }
        // else verification of user token case the url is not the login
        elseif (strpos($uri, '/api/usr/info') !== 0) {

            // retrieving token and verification in Db
            $token = $this->app->getContainer()->getRequest()->getParam('token');
            $isValid = $this->app->getContainer()->getManager()->getTable('users')->findOneBy(['TOKEN' => $token]);
            
            // stopping the request if client request token is wrong
//            if (!$isValid) {
//                $this->app->getResponse()->halt("client token: <".$token." > is incorrect !");
//            }
        }
    }

    private function displayApiRoutes() {
        $apiPack = $this->app->getContainer()->getConfig()->getPackByName('ApiPack');
        $list = $apiPack ? $apiPack->getRoutes() : [];
        $this->app->getResponse()
                ->setData($list)
                ->setFormat('xml')
                ->addMessage(' ----------THE ROUTES OF THE API ----------------- ')
                ->send();
    }

}
