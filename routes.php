<?php

use Phalcon\Mvc\Micro;
use Phalcon\Http\Response;
use Phalcon\Di\FactoryDefault;
use Phalcon\Db\Adapter\Pdo\Mysql as PdoMysql;

$loader = new \Phalcon\Loader();

$loader->registerNamespaces(
    [
        "UserData" => __DIR__."/models/"
    ]
);

$loader->register();

$di = new FactoryDefault();

$di->set(
    'db',
    function () {
        return new PdoMysql(
            [
                'host'     => 'db.cs.dal.ca',
                'username' => 'gayathrib',
                'password' => 'B00783576',
                'dbname'   => 'gayathrib',
            ]
        );
    }
);


$app = new Micro($di);

$app->get(
    "/getSearchResults/{name}",
    function ($name) use ($app)
    {

        $phql = "SELECT prodid from UserData\PRODUCT where prod_name LIKE :prod_name:";

        $prodids = $app->modelsManager->executeQuery(
            $phql,
            [
                "prod_name" => "%".$name."%"
            ]
        );

        $ids = array();

        foreach ($prodids as $prodid){
            $phql = "SELECT UserData\CATEGORY.categoryid,UserData\CATEGORY.category_name,UserData\PRODUCT_BY_STORE.prodid,UserData\PRODUCT_BY_STORE.storeid,UserData\PRODUCT.brandname,UserData\PRODUCT_BY_STORE.price,UserData\PRODUCT_BY_STORE.quantity,UserData\PRODUCT_BY_STORE.store_type,UserData\PRODUCT.prod_name,UserData\STORE.store_lat,UserData\STORE.store_long,UserData\STORE.store_name FROM UserData\PRODUCT_BY_STORE 
  	                      INNER JOIN UserData\PRODUCT ON UserData\PRODUCT_BY_STORE.prodid = UserData\PRODUCT.prodid
                          INNER JOIN UserData\STORE ON UserData\PRODUCT_BY_STORE.storeid = UserData\STORE.storeid
                          INNER JOIN UserData\CATEGORY ON UserData\PRODUCT.categoryid = UserData\CATEGORY.categoryid
                          WHERE UserData\PRODUCT.prodid = :prodid:
                          order by UserData\PRODUCT_BY_STORE.prodid, UserData\PRODUCT_BY_STORE.price";

            $products = $app->modelsManager->executeQuery(
                $phql,
                [
                    "prodid" => $prodid -> prodid
                ]
            );

            array_push($ids,$products);
        }

        $response = new Response();
        $response->setJsonContent(
            [
                "status" => "SUCCESS",
                "data" => $ids
            ]
        );
        return $response;
    }
);


$app->post(
    "/setCart",
    function () use ($app){
        $cart = $app -> request -> getJsonRawBody();

        $phql = 'INSERT INTO UserData\CART(userid,prodid,storeid,price,quantity) values(:userid:,:prodid:,:storeid:,:price:,:quantity:)';

        $status = $app->modelsManager->executeQuery(
            $phql,
            [
                "userid" => $cart -> user_id,
                "prodid" => $cart -> product_id,
                "storeid" => $cart -> store_id,
                "price" => $cart -> product_store_price,
                "quantity" => $cart -> product_store_quantity,
            ]
        );

        $response = new Response();

        if ($status->success() === True){
            $response->setStatusCode(201,"CREATED");
            $response->setJsonContent(
                [
                    "status" => "SUCCESS",
                    "addressStatus" => "PRODUCT_ADDED"
                ]
            );
        }
        else{
            $response->setStatusCode(409,"FAILURE");

            $errors = [];

            foreach ($status->getMessages() as $message) {
                $errors[] = $message->getMessage();
            }

            $response->setJsonContent(
                [
                    "status" => "SUCCESS",
                    "addressStatus" => "UNABLE_TO_ADD_TO_CART",
                    "errors" => $errors
                ]
            );
        }
        return $response;
    }
);


$app->post(
  "/setAddress",
  function () use ($app){
      $address = $app -> request -> getJsonRawBody();

      $phql = 'INSERT INTO UserData\USERADDR(userid,addr_line1,addr_line2,city,province,postal_code) values(:userid:,:addr_line1:,:addr_line2:,:city:,:province:,:postal_code:)';

      $status = $app->modelsManager->executeQuery(
          $phql,
          [
              "addr_line1" => $address -> address_line_1,
              "addr_line2" => $address -> address_line_2,
              "city" => $address -> city,
              "province" => $address -> province,
              "postal_code" => $address -> postal_code,
              "userid" => $address -> user_id,
          ]
      );

      $response = new Response();

      if ($status->success() === True){
          $response->setStatusCode(201,"CREATED");
          $response->setJsonContent(
              [
                  "status" => "SUCCESS",
                  "addressStatus" => "ADDRESS_ADDED"
              ]
          );
      }
      else{
          $response->setStatusCode(409,"FAILURE");

          $errors = [];

          foreach ($status->getMessages() as $message) {
              $errors[] = $message->getMessage();
          }

          $response->setJsonContent(
              [
                  "status" => "SUCCESS",
                  "addressStatus" => "ADDRESS_NOT_ADDED",
                  "errors" => $errors
              ]
          );
      }
      return $response;
  }
);

$app->post(
    "/updateAddress",
    function () use ($app){
        $address = null;
        $address = $app -> request -> getJsonRawBody();

        $phql = 'UPDATE UserData\USERADDR 
                 set addr_line1 = :addr_line1:,
                     addr_line2 = :addr_line2:,
                     city = :city:,
                     province = :province:, 
                     postal_code = :postal_code:
                 where addressid = :addressid:';

        $status = $app->modelsManager->executeQuery(
            $phql,
            [
                "addr_line1" => $address -> address_line_1,
                "addr_line2" => $address -> address_line_2,
                "city" => $address -> city,
                "province" => $address -> province,
                "postal_code" => $address -> postal_code,
                "addressid" => $address -> address_id
            ]
        );

        $response = new Response();

        if ($status->success() === True){
            $response->setStatusCode(201,"UPDATED");
            $response->setJsonContent(
                [
                    "status" => "SUCCESS",
                    "addressStatus" => "ADDRESS_UPDATED"
                ]
            );
        }
        else{
            $response->setStatusCode(409,"FAILURE");

            $errors = [];

            foreach ($status->getMessages() as $message) {
                $errors[] = $message->getMessage();
            }

            $response->setJsonContent(
                [
                    "status" => "SUCCESS",
                    "addressStatus" => "ADDRESS_NOT_UPDATED",
                    "errors" => $errors
                ]
            );
        }
        return $response;
    }
);


$app->post(
    "/deleteAddress",
    function () use ($app){
        $address = $app -> request -> getJsonRawBody();

        $phql = 'DELETE from UserData\USERADDR 
                 where addressid = :addressid:';

        $status = $app->modelsManager->executeQuery(
            $phql,
            [
                "addressid" => $address -> address_id,
            ]
        );

        $response = new Response();

        if ($status->success() === True){
            $response->setStatusCode(201,"DELETED");
            $response->setJsonContent(
                [
                    "status" => "SUCCESS",
                    "addressStatus" => "ADDRESS_DELETED"
                ]
            );
        }
        else{
            $response->setStatusCode(409,"FAILURE");

            $errors = [];

            foreach ($status->getMessages() as $message) {
                $errors[] = $message->getMessage();
            }

            $response->setJsonContent(
                [
                    "status" => "SUCCESS",
                    "addressStatus" => "ADDRESS_NOT_DELETED",
                    "errors" => $errors
                ]
            );
        }
        return $response;
    }
);


$app->post(
    "/checkLogin",
    function () use ($app){
        $user = $app -> request -> getJsonRawBody();

        $phql = 'SELECT password FROM UserData\USER where email = :emailid:';

        $user_password = $app->modelsManager->executeQuery(
            $phql,
            [
                "emailid" => $user->user_id,
            ]
        );

        $loginStatus = "LOGIN_FAILURE";

        if ($user_password[0] -> password === $user -> password){
            $loginStatus = "LOGIN_SUCCESS";
        }

        $response = new Response();
        $response->setStatusCode(201,"RETRIEVED");
        $response->setJsonContent(
            [
                "status" => "SUCCESS",
                "data" => $loginStatus,
            ]
        );

        return $response;
    }
);


$app->post(
    "/getAddress",
    function () use ($app){
        $user = $app -> request -> getJsonRawBody();

        $phql = 'SELECT * FROM UserData\USERADDR where userid = :id:';

        $addresses = $app->modelsManager->executeQuery(
            $phql,
            [
                "id" => $user->user_id,
            ]
        );

        $addressData = [];

        foreach ($addresses as $address){
            $addressData[] = [
                "addr_line1" => $address -> addr_line1,
                "addr_line2" => $address -> addr_line2,
                "city" => $address -> city,
                "province" => $address -> province,
                "postal_code" => $address -> postal_code,
                "userid" => $address -> userid,
            ];
        }

        $response = new Response();
        $response->setStatusCode(201,"RETRIEVED");
        $response->setJsonContent(
            [
                "status" => "SUCCESS",
                "data" => json_encode($addressData),
            ]
        );

        return $response;
    }
);


$app->post(
    "/setUser",
    function () use ($app){
        $user = $app -> request -> getJsonRawBody();

        $phql = 'INSERT INTO UserData\USER(firstname,lastname,email,contact,password) values(:firstname:,:lastname:,:email:,:contact:,:password:)';

        $status = $app->modelsManager->executeQuery(
            $phql,
            [
                "firstname" => $user -> firstName,
                "lastname" => $user -> lastName,
                "email" => $user -> email,
                "contact" => $user -> contact,
                "password" => $user -> password,
            ]
        );

        $response = new Response();

        if ($status->success() === True){
            $response->setStatusCode(201,"CREATED");
            $response->setJsonContent(
                [
                    "status" => "SUCCESS",
                    "addressStatus" => "USER_ADDED"
                ]
            );
        }
        else{
            $response->setStatusCode(409,"FAILURE");

            $errors = [];

            foreach ($status->getMessages() as $message) {
                $errors[] = $message->getMessage();
            }

            $response->setJsonContent(
                [
                    "status" => "SUCCESS",
                    "addressStatus" => "USER_NOT_ADDED",
                    "errors" => $errors
                ]
            );
        }
        return $response;
    }
);

$app->post(
    "/setCardDetails",
    function () use ($app){
        $user = $app -> request -> getJsonRawBody();

        $phql = 'INSERT INTO UserData\USERCARD(cardno,expiry_dt,cvv,cardtype,userid) values(:cardno:,:expiry_dt:,:cvv:,:cardtype:,:userid:)';

        $status = $app->modelsManager->executeQuery(
            $phql,
            [
                "cardno" => $user -> card_number,
                "expiry_dt" => $user -> expiry_date,
                "cvv" => $user -> cvv,
                "cardtype" => $user -> card_type,
                "userid" => $user -> user_id,
            ]
        );

        $response = new Response();

        if ($status->success() === True){
            $response->setStatusCode(201,"CREATED");
            $response->setJsonContent(
                [
                    "status" => "SUCCESS",
                    "addressStatus" => "CARD_ADDED"
                ]
            );
        }
        else{
            $response->setStatusCode(409,"FAILURE");

            $errors = [];

            foreach ($status->getMessages() as $message) {
                $errors[] = $message->getMessage();
            }

            $response->setJsonContent(
                [
                    "status" => "SUCCESS",
                    "addressStatus" => "CARD_NOT_ADDED",
                    "errors" => $errors
                ]
            );
        }
        return $response;
    }
);

$app->post(
    "/updateCardDetails",
    function () use ($app){
        $user = $app -> request -> getJsonRawBody();

        $phql = 'UPDATE UserData\USERCARD
                 SET cardno = :cardno:,
                     expiry_dt = :expiry_dt:,
                     cvv = :cvv:,
                     cardtype = :cardtype:
                 where cardid = :cardid:';

        $status = $app->modelsManager->executeQuery(
            $phql,
            [
                "cardno" => $user -> card_number,
                "expiry_dt" => $user -> expiry_date,
                "cvv" => $user -> cvv,
                "cardtype" => $user -> card_type,
                "cardid" => $user -> cardid,
            ]
        );

        $response = new Response();

        if ($status->success() === True){
            $response->setStatusCode(201,"UPDATED");
            $response->setJsonContent(
                [
                    "status" => "SUCCESS",
                    "cardStatus" => "CARD_DETAILS_UPDATED"
                ]
            );
        }
        else{
            $response->setStatusCode(409,"FAILURE");

            $errors = [];

            foreach ($status->getMessages() as $message) {
                $errors[] = $message->getMessage();
            }

            $response->setJsonContent(
                [
                    "status" => "SUCCESS",
                    "cardStatus" => "CARD_NOT_UPDATED",
                    "errors" => $errors
                ]
            );
        }
        return $response;
    }
);


$app->post(
    "/getCardDetails",
    function () use ($app){
        $user = $app -> request -> getJsonRawBody();

        $phql = 'SELECT * FROM UserData\USERCARD where userid = :id:';

        $cards = $app->modelsManager->executeQuery(
            $phql,
            [
                "id" => $user->user_id,
            ]
        );

        $cardsData = [];

        foreach ($cards as $card){
            $cardsData[] = [
                "card_id" => $card -> card_id,
                "card_number" => $card -> cardno,
                "expiry_date" => $card -> expiry_dt,
                "cvv" => $card -> cvv,
                "card_type" => $card -> cardtype,
            ];
        }

        $response = new Response();
        $response->setStatusCode(201,"RETRIEVED");
        $response->setJsonContent(
            [
                "status" => "SUCCESS",
                "data" => json_encode($cardsData),
            ]
        );

        return $response;
    }
);

$app->post(
    "/deleteCardDetails",
    function () use ($app){
        $user = $app -> request -> getJsonRawBody();

        $phql = 'DELETE FROM UserData\USERCARD where cardid = :cardid:';

        $cards = $app->modelsManager->executeQuery(
            $phql,
            [
                "cardid" => $user->cardid,
            ]
        );

        $response = new Response();

        if ($cards->success() === True){
            $response->setStatusCode(201,"DELETED");
            $response->setJsonContent(
                [
                    "status" => "SUCCESS",
                    "cardStatus" => "CARD_DETAILS_DELETED"
                ]
            );
        }
        else{
            $response->setStatusCode(409,"FAILURE");

            $errors = [];

            foreach ($status->getMessages() as $message) {
                $errors[] = $message->getMessage();
            }

            $response->setJsonContent(
                [
                    "status" => "SUCCESS",
                    "cardStatus" => "CARD_NOT_DELETED",
                    "errors" => $errors
                ]
            );
        }
        return $response;
    }
);



$app->handle();

$app->notFound(function () use ($app) {
    $app->response->setStatusCode(404, "Not Found")->sendHeaders();
    echo 'This is crazy, but this page was not found!';
});

?>
