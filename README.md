# Persistent storage for ZfcUser

[![Build Status](https://travis-ci.org/JouwWeb/JwPersistentUser.svg?branch=master)](https://travis-ci.org/JouwWeb/JwPersistentUser) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/JouwWeb/JwPersistentUser/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/JouwWeb/JwPersistentUser/?branch=master) [![Code Coverage](https://scrutinizer-ci.com/g/JouwWeb/JwPersistentUser/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/JouwWeb/JwPersistentUser/?branch=master)

`JwPersistentUser` safely keeps users logged on after browser sessions ends. It is an extension module for `ZfcUser`.

## Installation

1. The state that this application needs is stored in models implementing the `JwPersistentUser\Model\SerieTokenInterface`. By default this module is configured to use the bundled `JwPersistentUser
   Model\SerieToken` model. One can however configure another model that implements this interface.

   For example:
   ```
   <?php return [
       'jwpersistentuser' => [
           'serieTokenEntityClass' => 'User\Model\SerieToken'
       ]
   ];
   ```

2. Now we need to tell how to store this data. Therefore a service needs to be registered in the service manager. This service needs to implement `JwPersistentUser\Mapper\SerieTokenMapperInterface` and be registered under under `JwPersistentUser\Mapper\SerieToken` in the service manager.

   For example:
   ```
   <?php return [
       'service_manager' => [
           'JwPersistentUser\Mapper\SerieToken' => 'User\DatabaseSerieTokenMapper'
       ]
   ];
   ```