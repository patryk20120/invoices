<?php
include "config.php";

function checkForDataBaseTables(){
    global $dataBase;
    $tableInvoiceExists = false;
    if ($result = $dataBase->query("SHOW TABLES LIKE 'invoice'")) {
        if($result->num_rows == 1) {
            $tableInvoiceExists = true;
        }
    }
    if($tableInvoiceExists == false) {
        $sql = "CREATE TABLE IF NOT EXISTS `invoice` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `invoiceNumber` text COLLATE utf32_unicode_ci NOT NULL,
            `invoiceDate` text COLLATE utf32_unicode_ci NOT NULL,
            `invoiceSaleDate` text COLLATE utf32_unicode_ci NOT NULL,
            `invoicePaymentDate` text COLLATE utf32_unicode_ci NOT NULL,
            `invoicePaymentMethod` int(11) NOT NULL,
            `invoicePaymentBankName` text COLLATE utf32_unicode_ci,
            `invoicePaymentBankNumber` text COLLATE utf32_unicode_ci,
            `invoiceSellerName` text COLLATE utf32_unicode_ci NOT NULL,
            `invoiceSellerVATNumber` text COLLATE utf32_unicode_ci NOT NULL,
            `invoiceSellerStreet` text COLLATE utf32_unicode_ci NOT NULL,
            `invoiceSellerPostalCode` text COLLATE utf32_unicode_ci NOT NULL,
            `invoiceSellerCity` text COLLATE utf32_unicode_ci NOT NULL,
            `invoiceBuyerName` text COLLATE utf32_unicode_ci NOT NULL,
            `invoiceBuyerVATNumber` text COLLATE utf32_unicode_ci NOT NULL,
            `invoiceBuyerStreet` text COLLATE utf32_unicode_ci NOT NULL,
            `invoiceBuyerPostalCode` text COLLATE utf32_unicode_ci NOT NULL,
            `invoiceBuyerCity` text COLLATE utf32_unicode_ci NOT NULL,
            `invoiceMarkAsPaid` int(11) NOT NULL,
            PRIMARY KEY (`id`)
          ) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf32 COLLATE=utf32_unicode_ci;";
        if($dataBase->query($sql)){
            echo "Stworzono tabelę 'invoice' w bazie danych.<br>";
        }else{
            echo "Wystąpił błąd przy próbie dodania tabeli 'invoice' w bazie danych.<br>";
        }
    }

    $tableInvoiceProductsExists = false;
    if ($result = $dataBase->query("SHOW TABLES LIKE 'invoice_product'")) {
        if($result->num_rows == 1) {
            $tableInvoiceProductsExists = true;
        }
    }
    if($tableInvoiceProductsExists == false) {
        $sql = "CREATE TABLE IF NOT EXISTS `invoice_product` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `id_invoice` int(11) NOT NULL,
            `invoiceProductName` text COLLATE utf32_unicode_ci NOT NULL,
            `invoiceProductQty` int(11) NOT NULL,
            `invoiceProductCountType` int(11) NOT NULL,
            `invoiceProductVAT` int(11) NOT NULL,
            `invoiceProductPrice` float NOT NULL,
            PRIMARY KEY (`id`)
          ) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf32 COLLATE=utf32_unicode_ci;";
        if($dataBase->query($sql)){
            echo "Stworzono tabelę 'invoice_product' w bazie danych.<br>";
        }else{
            echo "Wystąpił błąd przy próbie dodania tabeli 'invoice_product' w bazie danych.<br>";
        }
    }
}

function addNewInvoice($invoiceNumber, $invoiceDate, $invoiceSaleDate, $invoicePaymentDate, $invoicePaymentMethod, $invoicePaymentBankName, $invoicePaymentBankNumber, $invoiceSellerName, $invoiceSellerVATNumber, $invoiceSellerStreet, $invoiceSellerPostalCode, $invoiceSellerCity, $invoiceBuyerName, $invoiceBuyerVATNumber, $invoiceBuyerStreet, $invoiceBuyerPostalCode, $invoiceBuyerCity, $invoiceMarkAsPaid){
    global $dataBase;
    $sql = "INSERT INTO invoice (invoiceNumber, invoiceDate, invoiceSaleDate, invoicePaymentDate, invoicePaymentMethod, invoicePaymentBankName, invoicePaymentBankNumber, invoiceSellerName, invoiceSellerVATNumber, invoiceSellerStreet, invoiceSellerPostalCode, invoiceSellerCity, invoiceBuyerName, invoiceBuyerVATNumber, invoiceBuyerStreet, invoiceBuyerPostalCode, invoiceBuyerCity, invoiceMarkAsPaid) 
                    VALUES ('$invoiceNumber','$invoiceDate','$invoiceSaleDate','$invoicePaymentDate',$invoicePaymentMethod,'$invoicePaymentBankName','$invoicePaymentBankNumber','$invoiceSellerName', '$invoiceSellerVATNumber', '$invoiceSellerStreet','$invoiceSellerPostalCode', '$invoiceSellerCity', '$invoiceBuyerName', '$invoiceBuyerVATNumber',  '$invoiceBuyerStreet', '$invoiceBuyerPostalCode', '$invoiceBuyerCity', $invoiceMarkAsPaid);";
    if($dataBase->query($sql)){
        $last_id = $dataBase->insert_id;  
        return $last_id;
    }else{
        return false;
    }
}


function getLastInvoiceID(){
    global $dataBase;
    $invoiceID = 1;
    if ($stmt = $dataBase->prepare("SELECT invoiceNumber FROM invoice ORDER BY id DESC LIMIT 1")) {
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $stmt->bind_result($invoiceID);
            $stmt->fetch();
            $invoiceIDEx = explode("/", $invoiceID);
            $invoiceID = intval($invoiceIDEx[0]);
            $invoiceID++;
        }
        $stmt->close();
    }
    return $invoiceID;
}

function addProductToInvoice($addInvoiceStatus, $invoiceProductName, $invoiceProductQty, $invoiceProductCountType, $invoiceProductVAT, $invoiceProductPrice){
    global $dataBase;
    $sql = "INSERT INTO invoice_product (id_invoice, invoiceProductName, invoiceProductQty, invoiceProductCountType, invoiceProductVAT, invoiceProductPrice) 
                    VALUES ($addInvoiceStatus,'$invoiceProductName', $invoiceProductQty,$invoiceProductCountType, $invoiceProductVAT, $invoiceProductPrice);";
    if($dataBase->query($sql)){
        $last_id = $dataBase->insert_id;  
        return $last_id;
    }else{
        return false;
    }
}

function getInvoiceAmount($invoiceID){
    global $dataBase;
    $invoiceAmount = 0;
    if ($stmt = $dataBase->prepare("SELECT invoiceProductQty, invoiceProductPrice FROM invoice_product WHERE id_invoice = $invoiceID;")) {
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $stmt->bind_result($invoiceProductQty, $invoiceProductPrice);
            while($stmt->fetch()){
                $invoiceAmount += ($invoiceProductQty * $invoiceProductPrice);
            }
        }
        $stmt->close();
    }
    return $invoiceAmount;
}

function markInvoiceAsPaid($invoiceID){
    global $dataBase;
    $sql = "UPDATE invoice SET invoiceMarkAsPaid = 1 WHERE id = $invoiceID;";
    if($dataBase->query($sql)){
        return true;
    }else{
        return false;
    }
}

function deleteInvoice($invoiceID){
    global $dataBase;
    $sql = "DELETE FROM invoice WHERE id = $invoiceID ;";  
    if($dataBase->query($sql)){
        return true;
    }else{
        return false;
    }
}

function getInvoiceDetails($invoiceID){
    global $dataBase, $invoicePaymentMethodArray;
    $invoiceDetails = null;
    if ($stmt = $dataBase->prepare("SELECT id, invoiceNumber, invoiceDate, invoiceSaleDate, invoicePaymentDate, invoicePaymentMethod, invoicePaymentBankName, invoicePaymentBankNumber, invoiceSellerName, invoiceSellerVATNumber, invoiceSellerStreet, invoiceSellerPostalCode, invoiceSellerCity, invoiceBuyerName, invoiceBuyerVATNumber,  invoiceBuyerStreet, invoiceBuyerPostalCode, invoiceBuyerCity, invoiceMarkAsPaid FROM invoice WHERE id = $invoiceID")) {
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $stmt->bind_result($id, $invoiceNumber, $invoiceDate, $invoiceSaleDate, $invoicePaymentDate, $invoicePaymentMethod, $invoicePaymentBankName, $invoicePaymentBankNumber, $invoiceSellerName, $invoiceSellerVATNumber, $invoiceSellerStreet, $invoiceSellerPostalCode, $invoiceSellerCity, $invoiceBuyerName, $invoiceBuyerVATNumber, $invoiceBuyerStreet, $invoiceBuyerPostalCode, $invoiceBuyerCity, $invoiceMarkAsPaid);
            $stmt->fetch();
            $invoiceDetails['id'] = $id;
            $invoiceDetails['invoiceNumber'] = $invoiceNumber;
            $invoiceDetails['invoiceDate'] = $invoiceDate;
            $invoiceDetails['invoiceSaleDate'] = $invoiceSaleDate;
            $invoiceDetails['invoicePaymentDate'] = $invoicePaymentDate;
            $invoiceDetails['invoicePaymentMethod'] = $invoicePaymentMethodArray[$invoicePaymentMethod];
            $invoiceDetails['invoicePaymentMethodID'] = $invoicePaymentMethod;
            $invoiceDetails['invoicePaymentBankName'] = $invoicePaymentBankName;
            $invoiceDetails['invoicePaymentBankNumber'] = $invoicePaymentBankNumber;
            $invoiceDetails['invoiceSellerName'] = $invoiceSellerName;
            $invoiceDetails['invoiceSellerVATNumber'] = $invoiceSellerVATNumber;
            $invoiceDetails['invoiceSellerStreet'] = $invoiceSellerStreet;
            $invoiceDetails['invoiceSellerPostalCode'] = $invoiceSellerPostalCode;
            $invoiceDetails['invoiceSellerCity'] = $invoiceSellerCity;
            $invoiceDetails['invoiceBuyerName'] = $invoiceBuyerName;
            $invoiceDetails['invoiceBuyerVATNumber'] = $invoiceBuyerVATNumber;
            $invoiceDetails['invoiceBuyerStreet'] = $invoiceBuyerStreet;
            $invoiceDetails['invoiceBuyerPostalCode'] = $invoiceBuyerPostalCode;
            $invoiceDetails['invoiceBuyerCity'] = $invoiceBuyerCity;
            $invoiceDetails['invoiceMarkAsPaid'] = $invoiceMarkAsPaid;
        }
        $stmt->close();
    }
    return $invoiceDetails;
}

function getInvoiceProducts($invoiceID){
    global $dataBase, $invoiceProductCountTypeArray, $invoiceProductVATArray;
    $productsArray = array();
    if ($stmt = $dataBase->prepare("SELECT id, invoiceProductName, invoiceProductQty, invoiceProductCountType, invoiceProductVAT, invoiceProductPrice FROM invoice_product WHERE id_invoice = $invoiceID")) {
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $stmt->bind_result($id, $invoiceProductName, $invoiceProductQty, $invoiceProductCountType, $invoiceProductVAT, $invoiceProductPrice);
            while($stmt->fetch()){
                $tmp['id'] =  $id; 
                $tmp['invoiceProductName'] =  $invoiceProductName; 
                $tmp['invoiceProductQty'] =  $invoiceProductQty; 
                $tmp['invoiceProductCountType'] =  $invoiceProductCountTypeArray[$invoiceProductCountType]; 
                $tmp['invoiceProductVAT'] =  $invoiceProductVATArray[$invoiceProductVAT]; 
                $tmp['invoiceProductPrice'] =  $invoiceProductPrice; 
                
                array_push($productsArray, $tmp);
            }
        }
        $stmt->close();
    }

    return $productsArray;
}