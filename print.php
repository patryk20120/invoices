<?php
include "./incl/core.php";

$invoiceDetails = array();
$invoiceProducts = array();
$invoiceID = 0;
if(isset($_GET['invoiceID'])){
    $invoiceDetails = getInvoiceDetails($_GET['invoiceID']);
    $invoiceProducts = getInvoiceProducts($_GET['invoiceID']);
    $invoiceID = $_GET['invoiceID'];
}else{
    header('Location: index.php');
	exit;
}
?>
<!DOCTYPE HTML>
<html lang="en">

<head>
    <link rel="stylesheet" href="./assets/css/bootstrap.min.css" />
    <link rel="stylesheet" href="./assets/css/style.css">

    <style>
    body {
        background: rgb(204, 204, 204);
    }

    page {
        background: white;
        display: block;
        margin: 0 auto;
        margin-bottom: 0.5cm;
        box-shadow: 0 0 0.5cm rgba(0, 0, 0, 0.5);
    }

    page[size="A4"] {
        width: 21cm;
        height: 29.7cm;
        padding: 0.3cm;
    }
    </style>

</head>

<body style="">
    <page size="A4">
        <h4>Faktura nr <?php echo $invoiceDetails['invoiceNumber'];?></h4>
        <hr>
        <div class="row">
            <div class="col-6">
                <p style="line-height: 1;">
                    <b>Sprzedawca:</b><br>
                    <?php echo $invoiceDetails['invoiceSellerName'];?><br>
                    <?php echo $invoiceDetails['invoiceSellerStreet'];?><br>
                    <?php echo $invoiceDetails['invoiceSellerPostalCode'];?><br>
                    NIP: <?php echo $invoiceDetails['invoiceSellerVATNumber'];?>
                </p>
            </div>
            <div class="col-6">
                <p style="line-height: 1;">
                    <b>Nabywca:</b><br>
                    <?php echo $invoiceDetails['invoiceBuyerName'];?><br>
                    <?php echo $invoiceDetails['invoiceBuyerStreet'];?><br>
                    <?php echo $invoiceDetails['invoiceBuyerPostalCode'];?><br>
                    NIP: <?php echo $invoiceDetails['invoiceBuyerVATNumber'];?>
                </p>
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-6">
                <p style="line-height: 1;">
                    Data wystawienia: <?php echo $invoiceDetails['invoiceDate'];?><br>
                    Data sprzedaży: <?php echo $invoiceDetails['invoiceSaleDate'];?>
                </p>
            </div>
            <div class="col-6">
                <p style="line-height: 1;">
                    Sposób płatności: <?php echo $invoiceDetails['invoicePaymentMethod'];?><br>
                    Termin płatności: <?php echo $invoiceDetails['invoicePaymentDate'];?>
                    <?php
                        if($invoiceDetails['invoicePaymentMethodID'] == 2){
                            echo "<br>Bank: ".$invoiceDetails['invoicePaymentBankName']."<br>";
                            echo "Numer konta: ".$invoiceDetails['invoicePaymentBankNumber']."<br>";
                        }
                    ?>
                </p>
            </div>
        </div>
        <table class="table">
            <thead>
                <tr>
                    <th scope="col">L.p.</th>
                    <th scope="col">Nazwa</th>
                    <th scope="col">Ilość</th>
                    <th scope="col">J.m.</th>
                    <th scope="col">Cena netto</th>
                    <th scope="col">Wartość netto</th>
                    <th scope="col">Stawka VAT</th>
                    <th scope="col">Kwota VAT</th>
                    <th scope="col">Wartność brutto</th>
                </tr>
            </thead>
            <tbody>

                <?php
    $index = 1;
    $totalNetto = 0;
    $totalVAT = 0;
    $totalBrutto = 0;
    foreach($invoiceProducts as $singleProduct){
        $VATNumber = intval(str_replace("%","",$singleProduct['invoiceProductVAT']));
        $productsTotalNetto = ($singleProduct['invoiceProductPrice'] * $singleProduct['invoiceProductQty']);
        $VATAmount = number_format((($VATNumber*$productsTotalNetto)/100), 2, '.', '');
        echo "<tr>
                <th scope='row'>$index</th>
                <td>".$singleProduct['invoiceProductName']."</td>
                <td>".$singleProduct['invoiceProductQty']."</td>
                <td>".$singleProduct['invoiceProductCountType']."</td>
                <td>".$singleProduct['invoiceProductPrice']." zł</td>
                <td>".$productsTotalNetto." zł</td>
                <td>".$singleProduct['invoiceProductVAT']."</td>
                <td>".$VATAmount." zł</td>
                <td>".($productsTotalNetto + $VATAmount)." zł</td>
            </tr>";
        $totalNetto += $productsTotalNetto;
        $totalVAT += $VATAmount;
        $totalBrutto += ($productsTotalNetto + $VATAmount);
        $index++;
    }

    echo "<tr>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td><b>Razem</b></td>
            <td>".$totalNetto." zł</td>
            <td></td>
            <td>".$totalVAT." zł</td>
            <td>".$totalBrutto." zł</td>
        </tr>";
    ?>
            </tbody>
            <caption>Razem do zapłaty: 
                <?php echo $totalBrutto;?> zł<br>
                Zapłacono:
                <?php 
                if($invoiceDetails['invoiceMarkAsPaid'] == 0){ 
                    echo "0 zł";
                }else{
                     echo "$totalBrutto zł";   
                }
                ?>
            </caption>
        </table>
    </page>

    <script src="./assets/js/jquery-3.4.1.min.js"></script>
    <script src="./assets/js/bootstrap.min.js"></script>
    <script src="./assets/js/script.js"></script>
</body>

</html>