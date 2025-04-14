<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <style>
        a {
            font-size: 20px;
        }
    </style>
</head>
<body>
    <form action="{{ route('checkout.process') }}" method="POST">
        @csrf
        <label for="goods_name">Goods Name:</label>
        <input type="text" id="goods_name" name="goods_name" required><br>
        <label for="out_trade_no">Trade Number:</label>
        <input type="text" id="out_trade_no" name="out_trade_no" required><br>
        <label for="txamt">Amount:</label>
        <input type="number" id="txamt" name="txamt" required><br>
        <button type="submit">Checkout</button>
    </form>
</body>
</html>
