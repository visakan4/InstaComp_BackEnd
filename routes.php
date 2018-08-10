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
                "quantity" => $cart -> product_store_quantity
            ]
        );

        $response = new Response();

        if ($status->success() === True){
			$data[] = ["cartStatus" => "PRODUCT_ADDED"];
            $response->setStatusCode(201,"CREATED");
            $response->setJsonContent(
                [
                    "status" => "SUCCESS",
					"data" => array(["cartStatus" => "PRODUCT_ADDED"])
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
					"data" => array(["cartStatus" => "UNABLE_TO_ADD_TO_CART"]),
                    "errors" => $errors
                ]
            );
        }
        return $response;
    }
);


$app->post(
    "/getCart",

    function () use ($app){
        $cart = $app -> request -> getJsonRawBody();

        $phql = "SELECT UserData\CART.cartid,UserData\CART.prodid, UserData\CART.storeid,UserData\CART.price,UserData\CART.quantity from UserData\CART where userid = :userid:";

        $prodids = $app->modelsManager->executeQuery(
            $phql,
            [
                "userid" => $cart -> user_id
            ]
        );

        $ids = array();

        foreach ($prodids as $prodid){

            $temp = $prodid;

            $phql = "SELECT UserData\PRODUCT.prod_name, UserData\PRODUCT.brandname, UserData\CATEGORY.categoryid FROM UserData\PRODUCT
                        INNER JOIN UserData\CATEGORY ON UserData\CATEGORY.categoryid = UserData\PRODUCT.categoryid
                        where prodid = :prodid:";

            $products = $app->modelsManager->executeQuery(
                $phql,
                [
                    "prodid" => $prodid -> prodid
                ]
            );

            $temp->product_details = $products;

            $phql = "SELECT UserData\STORE.store_name FROM UserData\STORE where UserData\STORE.storeid = :storeid:";

            $store = $app->modelsManager->executeQuery(
                $phql,
                [
                    "storeid" => $prodid -> storeid
                ]
            );

            $temp->store_details = $store;

            array_push($ids,$temp);
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
          $model = $status ->getModel();
          $response->setStatusCode(201,"CREATED");
          $response->setJsonContent(
              [
                  "status" => "SUCCESS",
                  "data" => array(["addressStatus" => "ADDRESS_ADDED",
								   "addressID" => $model ->addressid]),
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
                  "data" => array(["addressStatus" => "ADDRESS_NOT_ADDED"]),
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
                    "data" => array(["addressStatus" => "ADDRESS_UPDATED"])
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
                    "data" => array(["addressStatus" => "ADDRESS_NOT_UPDATED"]),
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
                    "data" => array(["addressStatus" => "ADDRESS_DELETED"])
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
                    "data" => array(["addressStatus" => "ADDRESS_NOT_DELETED"]),
                    "errors" => $errors
                ]
            );
        }
        return $response;
    }
);


$app->post(
    "/deleteCart",
    function () use ($app){
        $cart = $app -> request -> getJsonRawBody();

        $phql = 'DELETE FROM UserData\CART WHERE cartid = :cartid:';

        $status = $app->modelsManager->executeQuery(
            $phql,
            [
                "cartid" => $cart -> cartid,
            ]
        );

        $response = new Response();

        if ($status->success() === True){
            $response->setStatusCode(201,"DELETED");
            $response->setJsonContent(
                [
                    "status" => "SUCCESS",
                    "data" => array(["cartStatus" => "ITEM_DELETED_FROM_CART"])
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
                    "data" => array(["cartStatus" => "ITEM_NOT_DELETED_FROM_CART"]),
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

        $phql = 'SELECT userid,firstname,lastname FROM UserData\USER where email = :emailid: 
                 and password = :password:';

        $user_password = $app->modelsManager->executeQuery(
            $phql,
            [
                "emailid" => $user->user_id,
                "password" => (hash('sha512',"jkaasdxxczsdk9076vtiuy".$user -> password."fdghytbnmbaphutdrchnv"))
            ]
        );

        $login = [];

        if (count($user_password) != 0)
        {
            $login[] = [
                "userid" => $user_password[0] -> userid,
                "firstname" => $user_password[0] -> firstname,
                "lastname" => $user_password[0] -> lastname,
                "loginStatus" => "LOGIN_SUCCESS",
            ];

        }
        else
        {
            $login[] = ["loginStatus" => "LOGIN_FAILURE"];
        }

        $response = new Response();
        $response->setStatusCode(201,"RETRIEVED");
        $response->setJsonContent(
            [
                "status" => "SUCCESS",
                "data" => $login,
            ]
        );

        return $response;
    }
);


$app->post(
    "/getOrder",
    function () use ($app) {

        $orderInput = $app->request->getJsonRawBody();

        $phql = 'SELECT UserData\ORDERS.orderid,UserData\ORDERS.userid, UserData\ORDERS.addressid, UserData\ORDERS.orderprice, UserData\ORDERS.order_status, UserData\ORDERS.order_date, UserData\ORDERS.cardid, UserData\USERADDR.addr_line1, UserData\USERADDR.addr_line2, UserData\USERADDR.city, UserData\USERADDR.postal_code, UserData\USERADDR.province, UserData\USERCARD.cardid, UserData\USERCARD.cardno, UserData\USERCARD.cardtype FROM UserData\ORDERS
		            INNER JOIN UserData\USERADDR ON UserData\ORDERS.addressid = UserData\USERADDR.addressid
                    INNER JOIN UserData\USERCARD ON UserData\ORDERS.cardid = UserData\USERCARD.cardid
                    WHERE UserData\ORDERS.userid = :userid:';

        $orderids = $app->modelsManager->executeQuery(
            $phql,
            [
                "userid" => $orderInput -> user_id
            ]
        );

        $ids = array();

        foreach ($orderids as $orderid){

            $temp = $orderid;

            $phql = "SELECT UserData\PRODUCT_BY_ORDER.orderid, UserData\PRODUCT_BY_ORDER.prodid, UserData\PRODUCT_BY_ORDER.storeid,UserData\PRODUCT_BY_ORDER.price,UserData\PRODUCT_BY_ORDER.quantity,UserData\PRODUCT.prod_name,UserData\STORE.storeid, UserData\STORE.store_name from UserData\PRODUCT_BY_ORDER
	                  INNER JOIN UserData\PRODUCT ON UserData\PRODUCT_BY_ORDER.prodid = UserData\PRODUCT.prodid
                      INNER JOIN UserData\STORE ON UserData\PRODUCT_BY_ORDER.storeid = UserData\STORE.storeid
                      WHERE UserData\PRODUCT_BY_ORDER.orderid = :orderid:";

            $orders = $app->modelsManager->executeQuery(
                $phql,
                [
                    "orderid" => $orderid -> orderid
                ]
            );

            $temp->product_details = $orders;

            array_push($ids,$temp);
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
                "address_id" => $address -> addressid,
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
                "data" => $addressData,
            ]
        );

        return $response;
    }
);


$app->post(
    "/setUser",
    function () use ($app){
        $user = $app -> request -> getJsonRawBody();

		$phql = 'SELECT userid FROM UserData\USER where email = :emailid:';

        $found = $app->modelsManager->executeQuery(
            $phql,
            [
                "emailid" => $user->email
            ]
        );

        $response = new Response();

        if (count($found) != 0)
        {
			$response->setStatusCode(409,"FAILURE");

            $errors = [];

            foreach ($found->getMessages() as $message) {
                $errors[] = $message->getMessage();
            }

            $response->setJsonContent(
                [
                    "status" => "SUCCESS",
                    "data" => array(["userStatus" => "USER_NOT_ADDED"]),
                    "errors" => array("email already registered"),
                ]
            );
			return $response;
		}


        $phql = 'INSERT INTO UserData\USER(firstname,lastname,email,contact,password) values(:firstname:,:lastname:,:email:,:contact:,:password:)';

        $status = $app->modelsManager->executeQuery(
            $phql,
            [
                "firstname" => $user -> firstname,
                "lastname" => $user -> lastname,
                "email" => $user -> email,
                "contact" => $user -> contact,
                "password" => (hash('sha512',"jkaasdxxczsdk9076vtiuy".$user -> password."fdghytbnmbaphutdrchnv"))
			
            ]
        );

        if ($status->success() === True){
            $response->setStatusCode(201,"CREATED");
            $response->setJsonContent(
                [
                    "status" => "SUCCESS",
                    "data" => array(["userStatus" => "USER_ADDED"])
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
                    "data" => array(["userStatus" => "USER_NOT_ADDED"]),
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
            $model = $status->getModel();
            $response->setStatusCode(201,"CREATED");
            $response->setJsonContent(
                [
                    "status" => "SUCCESS",
                    "data" => array(["cardStatus" => "CARD_ADDED",
					   				 "cardID" => $model ->cardid])
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
                    "data" => array(["cardStatus" => "CARD_NOT_ADDED"]),
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
                    "data" => array(["cardStatus" => "CARD_DETAILS_UPDATED"])
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
                    "data" => array(["cardStatus" => "CARD_NOT_UPDATED"]),
                    "errors" => $errors
                ]
            );
        }
        return $response;
    }
);


$app->post(
    "/updateCartDetails",
    function () use ($app){
        $cart = $app -> request -> getJsonRawBody();

        $phql = 'UPDATE UserData\CART SET quantity = :quantity: where cartid = :cartid:';

        $status = $app->modelsManager->executeQuery(
            $phql,
            [
                "quantity" => $cart -> quantity,
                "cartid" => $cart -> cartid
            ]
        );

        $response = new Response();

        if ($status->success() === True){
            $response->setStatusCode(201,"UPDATED");
            $response->setJsonContent(
                [
                    "status" => "SUCCESS",
                    "data" => array(["cardStatus" => "CART_DETAILS_UPDATED"])
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
                    "data" => array(["cardStatus" => "CART_DETAILS_NOT_UPDATED"]),
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
                "card_id" => $card -> cardid,
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
                "data" => $cardsData,
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
                    "data" => array(["cardStatus" => "CARD_DETAILS_DELETED"])
                ]
            );
        }
        else{
            $response->setStatusCode(409,"FAILURE");

            $errors = [];

            foreach ($cards->getMessages() as $message) {
                $errors[] = $message->getMessage();
            }

            $response->setJsonContent(
                [
                    "status" => "SUCCESS",
                    "data" => array(["cardStatus" => "CARD_NOT_DELETED"]),
                    "errors" => $errors
                ]
            );
        }
        return $response;
    }
);


$app->post(
    "/deleteCartByUserId",
    function () use ($app){
        $user = $app -> request -> getJsonRawBody();

        $phql = 'DELETE FROM UserData\CART WHERE userid = :userid:';

        $delete_cart = $app->modelsManager->executeQuery(
            $phql,
            [
                "userid" => $user->user_id,
            ]
        );

        $response = new Response();

        if ($delete_cart->success() === True){
            $response->setStatusCode(201,"DELETED");
            $response->setJsonContent(
                [
                    "status" => "SUCCESS",
                    "data" => array(["cartStatus" => "CART_DELETED"])
                ]
            );
        }
        else{
            $response->setStatusCode(409,"FAILURE");

            $errors = [];

            foreach ($delete_cart->getMessages() as $message) {
                $errors[] = $message->getMessage();
            }

            $response->setJsonContent(
                [
                    "status" => "SUCCESS",
                    "data" => array(["cartStatus" => "CART_NOT_DELETED"]),
                    "errors" => $errors
                ]
            );
        }
        return $response;
    }
);


$app->post(
    "/setOrder",
    function () use ($app) {
        $order = $app->request->getJsonRawBody();

        $phql = 'INSERT INTO UserData\ORDERS(userid,addressid,orderprice,order_status,order_date,cardid) values(:userid:,:addressid:,:orderprice:,:orderstatus:,CURRENT_TIMESTAMP(),:cardid:)';

        $status = $app->modelsManager->executeQuery(
            $phql,
            [
                "userid" => $order->user_id,
                "addressid" => $order->address_id,
                "orderprice" => $order->order_price,
                "orderstatus" => $order->order_status,
                "cardid" => $order->card_id
            ]
        );

        $response = new Response();

        if ($status->success() === True){
            $model = $status->getModel();
            foreach ($order->product_details as $product) {
                $phql = 'INSERT INTO  UserData\PRODUCT_BY_ORDER (orderid,prodid,storeid,price,quantity) values(:orderid:,:prodid:,:storeid:,:price:,:quantity:)';
                $order = $app->modelsManager->executeQuery(
                    $phql,
                    [
                        "prodid" => $product->product_id,
                        "storeid" => $product->store_id,
                        "price" => $product->product_order_price,
                        "quantity" => $product->product_order_quantity,
                        "orderid" => $model->orderid
                    ]
                );

                if ($order->success() === True){

                    $phql = "UPDATE UserData\PRODUCT_BY_STORE 
                                SET quantity = quantity - '$product->product_order_quantity'
	                            WHERE UserData\PRODUCT_BY_STORE.prodid = :prodid: AND 
                                UserData\PRODUCT_BY_STORE.storeid = :storeid: AND 
                                UserData\PRODUCT_BY_STORE.quantity > 0";

                    $reduce_order = $app->modelsManager->executeQuery(
                        $phql,
                        [
                            "prodid" => $product->product_id,
                            "storeid" => $product->store_id
                        ]
                    );

                    if ($reduce_order->success() === False){
                        break;
                    }
                }
                else{
                    break;
                }
            }
            if ($order->success() === True){
                $response->setStatusCode(201,"CREATED");
                $response->setJsonContent(
                    [
                        "status" => "SUCCESS",
                        "data" => array(["orderStatus" => "ORDER_PLACED",
                            "orderID" => $model ->orderid])
                    ]
                );
            }
            else{
                $response->setStatusCode(409,"FAILURE");
                $errors = [];
                foreach ($order->getMessages() as $message) {
                    $errors[] = $message->getMessage();
                }
                $response->setJsonContent(
                    [
                        "status" => "SUCCESS",
                        "data" => array(["orderStatus" => "ORDER_NOT_PLACED"]),
                        "errors" => $errors
                    ]
                );
            }
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
                    "data" => array(["orderStatus" => "FAILED_PLACING_ORDER"]),
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
