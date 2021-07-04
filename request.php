<?php
include "./incl/core.php";

if(isset($_GET['action'], $_GET['invoiceID']) && $_GET['action'] == "markAsPaid"){
    echo markInvoiceAsPaid($_GET['invoiceID']);
}

if(isset($_GET['action'], $_GET['invoiceID']) && $_GET['action'] == "deleteInvoice"){
    echo deleteInvoice($_GET['invoiceID']);
}

if(isset($_GET['action']) && $_GET['action'] == "getInvoiceID"){
    echo getLastInvoiceID();
}
?>