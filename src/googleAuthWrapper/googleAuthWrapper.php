<?php

namespace GoogleAuthWrapper;

/**
 * Wrapper to simplify use of Google Client Library
 * 
 * see: - https://console.cloud.google.com/apis/ for account
 */
class GoogleAuthWrapper {

    /**
     * Instanciate Google Client library
     * 
     * @param string $googleClientId - from API configuration
     * @param string $googleClientSecret - from API configuration
     * @param string $redirectUrl - redirecting url from goodle once authenticated
     * 
     * @return void
     */
    public function __construct($googleClientId, $googleClientSecret, $redirectUrl)
    {
        $client = new Google_Client();
        $client->setClientId($googleClientId);
        $client->setClientSecret($googleClientSecret);
        $client->setRedirectUri($redirectUrl);
        $client->addScope('email');

        $this->client = $client;
        $this->redirectUrl = $redirectUrl;
    }

    /**
     * Checks for code passed back from google
     * Returns true when access token is set
     * 
     * @return boolean
     */
    function checkForCode()
    {
        if (isset($_GET['code'])) {
            $token = $this->client->fetchAccessTokenWithAuthCode($_GET['code']);
        
            $this->client->setAccessToken($token);
        
            $_SESSION['access_token'] = $token;
        
            return true;
        }

        return false;
    }

    /**
     * Test for session tokeen and validate if set
     * 
     * @return boolean
     */
    function isSessionTokenSet()
    {
        if (!empty($_SESSION['access_token'])) {
        
            $this->client->setAccessToken($_SESSION['access_token']);

            if ($this->client->isAccessTokenExpired()) {
                unset($_SESSION['access_token']);
            }
        }

        if (empty($_SESSION['access_token'])) {
            return false;
        }

        return true;
    }

    /**
     * Get credentials from client
     * 
     * @return string
     */
    function getEmail()
    {
        $oauth2 = new Google_Service_Oauth2($this->client);
        $userInfo = $oauth2->userinfo->get();
        
        return $userInfo->getEmail();
    }

    /**
     * Check client session and return email / redirect or false
     * depending on status of login process
     * 
     * @return mixed
     */
    function doLogin()
    {
        session_start();

        if ($this->checkForCode()) {
            $url = filter_var($redirectUrl, FILTER_SANITIZE_URL);
            return ['redirect' => $url];
        }
        
        if (!$this->isSessionTokenSet()) {
            $authUrl = $this->client->createAuthUrl();
            $url = filter_var($authUrl, FILTER_SANITIZE_URL);
            return ['redirect' => $url];
        }

        if ($this->client->getAccessToken()) {
            return $this->getEmail();
        }
        
        return false;
    }
}
