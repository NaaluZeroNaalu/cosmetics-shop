<?php

@include 'config.php';

session_start();

$user_id = $_SESSION['user_id'];

if(!isset($user_id)){
   header('location:login.php');
};

if(isset($_POST['order'])){

   $name = $_POST['name'];
   $name = filter_var($name, FILTER_SANITIZE_STRING);
   $number = $_POST['number'];
   $number = filter_var($number, FILTER_SANITIZE_STRING);
   $email = $_POST['email'];
   $email = filter_var($email, FILTER_SANITIZE_STRING);
   $method = $_POST['method'];
   $method = filter_var($method, FILTER_SANITIZE_STRING);
   $address = 'flat no. '. $_POST['flat'] .' '. $_POST['street'] .' '. $_POST['city'] .' '. $_POST['state'] .' '. $_POST['country'] .' - '. $_POST['pin_code'];
   $address = filter_var($address, FILTER_SANITIZE_STRING);
   $placed_on = date('d-M-Y');

   $cart_total = 0;
   $cart_products[] = '';

   $cart_query = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ?");
   $cart_query->execute([$user_id]);
   if($cart_query->rowCount() > 0){
      while($cart_item = $cart_query->fetch(PDO::FETCH_ASSOC)){
         $cart_products[] = $cart_item['name'].' ( '.$cart_item['quantity'].' )';
         $sub_total = ($cart_item['price'] * $cart_item['quantity']);
         $cart_total += $sub_total;
      };
   };

   $total_products = implode(', ', $cart_products);

   $order_query = $conn->prepare("SELECT * FROM `orders` WHERE name = ? AND number = ? AND email = ? AND method = ? AND address = ? AND total_products = ? AND total_price = ?");
   $order_query->execute([$name, $number, $email, $method, $address, $total_products, $cart_total]);

   if($cart_total == 0){
      $message[] = 'your cart is empty';
   }elseif($order_query->rowCount() > 0){
      $message[] = 'order placed already!';
   }else{
      $insert_order = $conn->prepare("INSERT INTO `orders`(user_id, name, number, email, method, address, total_products, total_price, placed_on) VALUES(?,?,?,?,?,?,?,?,?)");
      $insert_order->execute([$user_id, $name, $number, $email, $method, $address, $total_products, $cart_total, $placed_on]);
      $delete_cart = $conn->prepare("DELETE FROM `cart` WHERE user_id = ?");
      $delete_cart->execute([$user_id]);
      $message[] = 'order placed successfully!';
   }

}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>checkout</title>

   <link rel="stylesheet" href="css/pay.css">
   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>
   
<?php include 'header.php'; ?>

<section class="display-orders">

   <?php
      $cart_grand_total = 0;
      $select_cart_items = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ?");
      $select_cart_items->execute([$user_id]);
      if($select_cart_items->rowCount() > 0){
         while($fetch_cart_items = $select_cart_items->fetch(PDO::FETCH_ASSOC)){
            $cart_total_price = ($fetch_cart_items['price'] * $fetch_cart_items['quantity']);
            $cart_grand_total += $cart_total_price;
   ?>
   <p> <?= $fetch_cart_items['name']; ?> <span>(<?= 'Rs'.$fetch_cart_items['price'].'/- x '. $fetch_cart_items['quantity']; ?>)</span> </p>
   <?php
    }
   }else{
      echo '<p class="empty">your cart is empty!</p>';
   }
   ?>
   <div class="grand-total">grand total : <span>Rs<?= $cart_grand_total; ?>/-</span></div>
</section>

<section class="checkout-orders">

<div class="container">
        <div class="wrapper">
          
            <div class="checkout-text">
    
                <div class="back-to-home">
                    <a href="">Home page</a>
                    <img src="https://img.icons8.com/sf-regular/48/aaaaaa/forward.png" alt="Arrow" height="15px" width="15px" />
                    <a href="">Payment</a>
                </div>

                <div class="pay-button">
                    <button disabled>
                        <span>Payment Details</span>
                    </button>
                </div>

                <div class="or-line">
                    
                </div>
                <div class="country-region">
                    <label>SELECT PAYMENT METHOD</label>
                    <select id="method" oninput="payment()">
                        <option>Card</option>
                        <option>Cash on delivery</option>
                    </select>
                    
                </div>
    
                <div class="card-details">
                    <label>Card details</label>
                    <div class="card-number">
                        <input type="number" id="card" placeholder="0000 0000 0000 0000" />
                        <div>
                            <img src="https://img.icons8.com/color/48/000000/visa.png" height="20px" width="20px" />
                            <img src="https://img.icons8.com/color/48/000000/mastercard.png" height="20px" width="20px" />
                            <img src="https://img.icons8.com/color/48/000000/amex.png" height="20px" width="20px" />
                        </div>
                    </div>
                    <div class="card-info">
                        <div class="mm-yy">
                            <input type="text" id="carda" placeholder="MM / YY" maxlength="5" />
                        </div>
                        <div class="cvc">
                            <input type="text" id="cardb" placeholder="CVC" maxlength="3" />
                            <img src="https://img.icons8.com/ios/50/000000/card-verification-value.png" height="20px" width="20px" />
                        </div>
                    </div>
                </div>
    
                <div class="owner-name">
                    <label>name</label>
                    <input type="text" placeholder="Your name" />
                </div>
    
                <div class="country-region">
                    <label>Address</label>
                    <input type="text" placeholder="Address" />
                    <label>Pincode</label>
                    <input type="number" placeholder="000000" />
                </div>
    
                <button class="buy-button" ><a href="thankyou.html">Pay</a></button>
    
            </div>
        </div>
    
        <script src="js/pay.js"></script>

</section>








<?php include 'footer.php'; ?>

<script src="js/script.js"></script>

</body>
</html>