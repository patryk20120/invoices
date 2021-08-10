<?php
//includew
//banrch 3.0
include "./incl/core.php";

if(isset($_POST['addNewInvoice'])){
    $invoiceNumber = mysqli_real_escape_string($dataBase, $_POST['invoiceNumber']);
    $invoiceDate = mysqli_real_escape_string($dataBase, $_POST['invoiceDate']);
    $invoiceSaleDate = mysqli_real_escape_string($dataBase, $_POST['invoiceSaleDate']);
    $invoiceSaleDateDivide = explode('-', $invoiceSaleDate);
    $invoiceSaleDate = $invoiceSaleDateDivide[2].'.'.$invoiceSaleDateDivide[1].'.'.$invoiceSaleDateDivide[0];
    $invoicePaymentDate = mysqli_real_escape_string($dataBase, $_POST['invoicePaymentDate']);
    $invoicePaymentDateDivide = explode('-', $invoicePaymentDate);
    $invoicePaymentDate = $invoicePaymentDateDivide[2].'.'.$invoicePaymentDateDivide[1].'.'.$invoicePaymentDateDivide[0];
    $invoicePaymentMethod = mysqli_real_escape_string($dataBase, $_POST['invoicePaymentMethod']);
    $invoicePaymentBankName = mysqli_real_escape_string($dataBase, $_POST['invoicePaymentBankName']);
    $invoicePaymentBankNumber = mysqli_real_escape_string($dataBase, $_POST['invoicePaymentBankNumber']);
    $invoiceSellerName = mysqli_real_escape_string($dataBase, $_POST['invoiceSellerName']);
    $invoiceSellerVATNumber = mysqli_real_escape_string($dataBase, $_POST['invoiceSellerVATNumber']);
    $invoiceSellerStreet = mysqli_real_escape_string($dataBase, $_POST['invoiceSellerStreet']);
    $invoiceSellerPostalCode = mysqli_real_escape_string($dataBase, $_POST['invoiceSellerPostalCode']);
    $invoiceSellerCity = mysqli_real_escape_string($dataBase, $_POST['invoiceSellerCity']);
    $invoiceBuyerName = mysqli_real_escape_string($dataBase, $_POST['invoiceBuyerName']);
    $invoiceBuyerVATNumber = mysqli_real_escape_string($dataBase, $_POST['invoiceBuyerVATNumber']);
    $invoiceBuyerStreet = mysqli_real_escape_string($dataBase, $_POST['invoiceBuyerStreet']);
    $invoiceBuyerPostalCode = mysqli_real_escape_string($dataBase, $_POST['invoiceBuyerPostalCode']);
    $invoiceBuyerCity = mysqli_real_escape_string($dataBase, $_POST['invoiceBuyerCity']);
    
    $invoiceProductName = $_POST['invoiceProductName'];
    $invoiceProductQty = $_POST['invoiceProductQty'];
    $invoiceProductCountType = $_POST['invoiceProductCountType'];
    $invoiceProductVAT = $_POST['invoiceProductVAT'];
    $invoiceProductPrice = $_POST['invoiceProductPrice'];

    $invoiceMarkAsPaid = 0;
    if(isset($_POST['invoiceMarkAsPaid'])){
        $invoiceMarkAsPaid = 1;
    }

    
    $addInvoiceStatus = addNewInvoice($invoiceNumber, $invoiceDate, $invoiceSaleDate, $invoicePaymentDate, $invoicePaymentMethod, $invoicePaymentBankName, $invoicePaymentBankNumber, $invoiceSellerName, $invoiceSellerVATNumber, $invoiceSellerStreet, $invoiceSellerPostalCode, $invoiceSellerCity, $invoiceBuyerName, $invoiceBuyerVATNumber, $invoiceBuyerStreet, $invoiceBuyerPostalCode, $invoiceBuyerCity, $invoiceMarkAsPaid);

    if($addInvoiceStatus != false){
        $productIndex = 0;
        for($i = 0; $i <= sizeof($invoiceProductName)-1; $i++){
            if($invoiceProductName[$i] != ""){
                addProductToInvoice($addInvoiceStatus, $invoiceProductName[$i], $invoiceProductQty[$i], $invoiceProductCountType[$i], $invoiceProductVAT[$i], $invoiceProductPrice[$i]);
                $productIndex++;
            }
        }
    }
}
?>
<!doctype html>

<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>IAI ZADANIE - FAKTURY</title>
    <meta name="description" content="">
    <meta name="author" content="Patryk Garstecki">

    <link rel="stylesheet" href="./assets/css/bootstrap.min.css" />
    <link rel="stylesheet" href="./assets/css/style.css">
</head>

<body>
    <div id="page-holder">
        <div class="row justify-content-center">
            <div class="col-sm-12 col-md-10 col-lg-8">
                <button type="button" class="btn btn-success generate-new-invoice-button">Generuj nową fakturę</button>
            </div>
        </div>
        <div class="row justify-content-center generate-new-invoice">
            <div class="col-sm-12 col-md-10 col-lg-8">
                <h2>Dodaj nową fakturę</h2>
                <form action="index.php" method="POST" class="g-3 needs-validation">
                    <h4>Dane faktury</h4>
                    <!-- INVOICE NUMBER -->
                    <div class="form-row">
                        <div class="form-group col-md-12">
                            <!--<label for="invoiceNumber">Numer faktury</label>-->
                            <input type="hidden" name="invoiceNumber" id="invoiceNumber"
                                value="<?php echo getLastInvoiceID().'/'.date('n').'/'.date('Y');?>">
                        </div>
                        <div class="form-group col-md-12">
                            <p class="mb-0">Numer faktury</p>
                            <h2 class="pt-0"><span id="invoiceID"><?php echo getLastInvoiceID().'</span>/'.date('n').'/'.date('Y');?></h2>
                        </div>
                    </div>
                    <!-- INVOICE DATE & SALE DATE -->
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="invoiceDate">Data wystawienia</label>
                            <input required type="text" class="form-control" name="invoiceDate" id="invoiceDate"
                                value="<?php echo date('d.m.Y');?>" readonly>

                        </div>
                        <div class="form-group col-md-6">
                            <label for="invoiceSaleDate">Data sprzedaży</label>
                            <input required type="date" class="form-control" name="invoiceSaleDate"
                                id="invoiceSaleDate" placeholder="">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="invoicePaymentDate">Termin płatności</label>
                            <input required type="date" class="form-control" name="invoicePaymentDate" id="invoicePaymentDate"
                                placeholder="">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="invoicePaymentMethod">Sposób płatności</label>
                            <select name="invoicePaymentMethod" id="invoicePaymentMethod" class="form-control">
                                <option value="1" selected>Płatność gotówką</option>
                                <option value="2">Płatność przelewem</option>
                                <option value="3">Płatność kartą</option>
                                <option value="4">Płatność online</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-row bank-account-details">
                        <div class="form-group col-md-4">
                            <label for="invoicePaymentBankName">Nazwa banku</label>
                            <input type="text" class="form-control" name="invoicePaymentBankName"
                                id="invoicePaymentBankName" placeholder="">
                        </div>
                        <div class="form-group col-md-8">
                            <label for="invoicePaymentBankNumber">Numer konta</label>
                            <input type="text" class="form-control" name="invoicePaymentBankNumber"
                                id="invoicePaymentBankNumber" placeholder="">
                        </div>
                    </div>
                    <hr>

                    <h4>Dane sprzedawcy</h4>
                    <!-- SELLER DETAILS -->
                    <div class="form-row">
                        <div class="form-group col-md-8">
                            <label for="invoiceSellerName">Nazwa sprzedawcy</label>
                            <input required type="text" class="form-control" name="invoiceSellerName"
                                id="invoiceSellerName" placeholder="">
                        </div>
                        <div class="form-group col-md-4">
                            <label for="invoiceSellerVATNumber">NIP sprzedawcy</label>
                            <input required type="text" class="form-control" name="invoiceSellerVATNumber"
                                id="invoiceSellerVATNumber" placeholder="">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-4 col-sm-12">
                            <label for="invoiceSellerStreet">Ulica i numer</label>
                            <input required type="text" class="form-control" name="invoiceSellerStreet"
                                id="invoiceSellerStreet" placeholder="">
                        </div>
                        <div class="form-group col-md-4 col-sm-6">
                            <label for="invoiceSellerPostalCode">Kod pocztowy</label>
                            <input required type="text" class="form-control" name="invoiceSellerPostalCode"
                                id="invoiceSellerPostalCode" placeholder="">
                        </div>
                        <div class="form-group col-md-4 col-sm-6">
                            <label for="invoiceSellerCity">Miasto</label>
                            <input required type="text" class="form-control" name="invoiceSellerCity"
                                id="invoiceSellerCity" placeholder="">
                        </div>
                    </div>
                    <hr>

                    <h4>Dane nabywcy</h4>
                    <!-- BUYER DETAILS -->
                    <div class="form-row">
                        <div class="form-group col-md-8">
                            <label for="invoiceBuyerName">Nazwa nabywcy</label>
                            <input required type="text" class="form-control" name="invoiceBuyerName"
                                id="invoiceBuyerName" placeholder="">
                        </div>
                        <div class="form-group col-md-4">
                            <label for="invoiceBuyerVATNumber">NIP nabywcy</label>
                            <input required type="text" class="form-control" name="invoiceBuyerVATNumber"
                                id="invoiceBuyerVATNumber" placeholder="">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-4 col-sm-12">
                            <label for="invoiceBuyerStreet">Ulica i numer</label>
                            <input required type="text" class="form-control" name="invoiceBuyerStreet"
                                id="invoiceBuyerStreet" placeholder="">
                        </div>
                        <div class="form-group col-md-4 col-sm-6">
                            <label for="invoiceBuyerPostalCode">Kod pocztowy</label>
                            <input required type="text" class="form-control" name="invoiceBuyerPostalCode"
                                id="invoiceBuyerPostalCode" placeholder="">
                        </div>
                        <div class="form-group col-md-4 col-sm-6">
                            <label for="invoiceBuyerCity">Miasto</label>
                            <input required type="text" class="form-control" name="invoiceBuyerCity"
                                id="invoiceBuyerCity" placeholder="">
                        </div>
                    </div>
                    <hr>

                    <h4>Pozycje</h4>
                    <!-- INVOICE PRODUCTS -->
                    <div class="invoice-lines">
                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label for="invoiceProductName-1">Nazwa towaru / usługi</label>
                                <input type="text" class="form-control" name="invoiceProductName[]"
                                    id="invoiceProductName-1" placeholder="">
                            </div>
                            <div class="form-group col-md-1">
                                <label for="invoiceProductQty-1">Ilość</label>
                                <input type="number" step="1" min="1" class="form-control" name="invoiceProductQty[]"
                                    id="invoiceProductQty-1" placeholder="">
                            </div>
                            <div class="form-group col-md-2">
                                <label for="invoiceProductCountType-1">J.m.</label>
                                <select name="invoiceProductCountType[]" id="invoiceProductCountType-1"
                                    class="form-control">
                                    <option value="1" selected>szt.</option>
                                    <option value="2">kg</option>
                                    <option value="3">op.</option>
                                </select>
                            </div>
                            <div class="form-group col-md-2">
                                <label for="invoiceProductVAT-1">VAT</label>
                                <select name="invoiceProductVAT[]" id="invoiceProductVAT-1" class="form-control">
                                    <option value="1" selected>23%</option>
                                    <option value="2">8%</option>
                                    <option value="3">5%</option>
                                    <option value="4">0%</option>
                                    <option value="5">z.w.</option>
                                </select>
                            </div>
                            <div class="form-group col-md-3">
                                <label for="invoiceProductPrice-1">Cena jednostkowa netto</label>
                                <input type="number" step="0.01" min="0.01" class="form-control" name="invoiceProductPrice[]"
                                    id="invoiceProductPrice-1" placeholder="">
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary add-next-line">Dodaj kolejną pozycję</button>
                    <hr>
                    <div class="form-group">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="invoiceMarkAsPaid"
                                id="invoiceMarkAsPaid">
                            <label class="form-check-label" for="invoiceMarkAsPaid">
                                Oznacz jako zapłaconą
                            </label>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary" name="addNewInvoice">Generuj fakturę</button>
                </form>
            </div>
        </div>

        <div class="row justify-content-center">
            <table class="col-sm-12 col-md-10 col-lg-10 table table-sm mt-5 table-responsive-md">
                <thead>
                    <tr>
                        <th scope="col">Numer</th>
                        <th scope="col">Data wystawienia</th>
                        <th scope="col">Sposób płatności</th>
                        <th scope="col">Nabywca</th>
                        <th scope="col">Sprzedawca</th>
                        <th scope="col">Kwota netto</th>
                        <th scope="col">Zapłacono</th>
                        <th scope="col">Akcje</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        if ($stmt = $dataBase->prepare("SELECT id, invoiceNumber, invoiceDate, invoicePaymentMethod, invoiceBuyerName, invoiceSellerName, invoiceMarkAsPaid FROM invoice ORDER BY id DESC;")) {
                            $stmt->execute();
                            $stmt->store_result();
                            if ($stmt->num_rows > 0) {
                                $stmt->bind_result($invoiceID, $invoiceNumber, $invoiceDate, $invoicePaymentMethod, $invoiceBuyerName, $invoiceSellerName, $invoiceMarkAsPaid);
                                while($stmt->fetch()){
                                    if($invoiceMarkAsPaid == 0){
                                        echo "<tr>";
                                    }else{
                                        echo "<tr class='table-success'>";
                                    }
                                    echo "<th scope='row'>$invoiceNumber</th>";
                                    echo "<td>$invoiceDate</td>";
                                    echo "<td>".$invoicePaymentMethodArray[$invoicePaymentMethod]."</td>";
                                    echo "<td>$invoiceBuyerName</td>";
                                    echo "<td>$invoiceSellerName</td>";
                                    echo "<td>".getInvoiceAmount($invoiceID)." zł</td>";
                                    if($invoiceMarkAsPaid == 0){
                                        echo "<td><button invoiceID='$invoiceID' type='button' class='btn btn-success btn-sm mark-as-paid-button'>Oznacz jako zapłaconą</button></td>";
                                    }else{
                                        echo "<td>TAK</td>";
                                    }
                                    echo "<td>
                                            <a href='print.php?invoiceID=$invoiceID'  target='_blank' class='btn btn-primary btn-sm'>Drukuj</a> 
                                            <button invoiceID='$invoiceID' type='button' class='btn btn-danger btn-sm delete-invoice-button'>Usuń</button>
                                        </td>";
                                    echo "</tr>";
                                }
                            }
                            $stmt->close();
                        }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <div aria-live="polite" aria-atomic="true" class="d-flex justify-content-center align-items-center"
        style="min-height: 200px;">

        <div class="toast toast1" data-delay="3000" data-autohide="true" data-animation="true" role="alert"
            aria-live="assertive" aria-atomic="true">
            <div class="toast-header">
                <img src="./assets/images/success.png" style="width: 20px; height: auto;" class="rounded mr-2">
                <strong class="mr-auto">Informacja</strong>
                <button type="button" class="ml-2 mb-1 close" data-dismiss="toast" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="toast-body">
                Oznaczono fakturę jako zapłaconą.
            </div>
        </div>
    </div>

    <div aria-live="polite" aria-atomic="true" class="d-flex justify-content-center align-items-center"
        style="min-height: 200px;">

        <div class="toast toast2" data-delay="3000" data-autohide="true" data-animation="true" role="alert"
            aria-live="assertive" aria-atomic="true">
            <div class="toast-header">
                <img src="./assets/images/success.png" style="width: 20px; height: auto;" class="rounded mr-2">
                <strong class="mr-auto">Informacja</strong>
                <button type="button" class="ml-2 mb-1 close" data-dismiss="toast" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="toast-body">
                Usunięto fakturę.
            </div>
        </div>
    </div>

    <div aria-live="polite" aria-atomic="true" class="d-flex justify-content-center align-items-center"
        style="min-height: 200px;">

        <div class="toast toast3" data-delay="3000" data-autohide="true" data-animation="true" role="alert"
            aria-live="assertive" aria-atomic="true">
            <div class="toast-header">
                <img src="./assets/images/error.png" style="width: 20px; height: auto;" class="rounded mr-2">
                <strong class="mr-auto">Błąd</strong>
                <button type="button" class="ml-2 mb-1 close" data-dismiss="toast" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="toast-body">
                Wystąpił nieoczekiwany błąd.
            </div>
        </div>
    </div>

    <script src="./assets/js/jquery-3.4.1.min.js"></script>
    <script src="./assets/js/bootstrap.min.js"></script>
    <script src="./assets/js/script.js"></script>
</body>

</html>
