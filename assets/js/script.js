$(document).ready(function() {
    $('.generate-new-invoice').hide();
    $('.bank-account-details').hide();

    $('.generate-new-invoice-button').on('click', function() {
        $('.generate-new-invoice').toggle();
    });

    $('#invoicePaymentMethod').on('change', function() {
        var val = $(this).val();
        if (val == 2) {
            $('.bank-account-details').show();
        } else {
            $('.bank-account-details').hide();
        }
    });

    $('.add-next-line').on('click', function() {
        $('.invoice-lines').append("<div class='form-row'> <div class='form-group col-md-4'> <label for='invoiceProductName-1'>Nazwa towaru / usługi</label> <input type='text' class='form-control' name='invoiceProductName[]' id='invoiceProductName-1' placeholder=''> </div><div class='form-group col-md-1'> <label for='invoiceProductQty-1'>Ilość</label> <input type='number'  step='1' min='1' class='form-control' name='invoiceProductQty[]' id='invoiceProductQty-1' placeholder=''> </div><div class='form-group col-md-2'> <label for='invoiceProductCountType-1'>J.m.</label> <select name='invoiceProductCountType[]' id='invoiceProductCountType-1' class='form-control'> <option value='1' selected>szt.</option> <option value='2'>kg</option> <option value='3'>op.</option> </select> </div><div class='form-group col-md-2'> <label for='invoiceProductVAT-1'>VAT</label> <select name='invoiceProductVAT[]' id='invoiceProductVAT-1' class='form-control'> <option value='1' selected>23%</option> <option value='2'>8%</option> <option value='3'>5%</option> <option value='4'>0%</option> <option value='5'>z.w.</option> </select> </div><div class='form-group col-md-3'> <label for='invoiceProductPrice-1'>Cena jednostkowa netto</label> <input type='number' step='0.01' min='0.01' class='form-control' name='invoiceProductPrice[]' id='invoiceProductPrice-1' placeholder=''> </div></div>");
    });

    $('.mark-as-paid-button').on('click', function() {
        var root = this;
        var id_invoice = $(this).attr("invoiceID");
        var urlString = "./request.php?action=markAsPaid&invoiceID=" + id_invoice;
        $.ajax({
            type: "GET",
            url: urlString,
            cache: false,
            success: function(data) {
                if (data == "1") {
                    $('.toast1').toast('show');
                    $(root).closest("tr").addClass("table-success");
                    $(root).closest("td").html("TAK");
                } else {
                    $('.toast3').toast('show');
                }
            }
        });
    });

    $('.delete-invoice-button').on('click', function() {
        var root = this;
        var id_invoice = $(this).attr("invoiceID");
        var urlString = "./request.php?action=deleteInvoice&invoiceID=" + id_invoice;
        $.ajax({
            type: "GET",
            url: urlString,
            cache: false,
            success: function(data) {
                if (data == "1") {
                    $('.toast2').toast('show');
                    $(root).closest("tr").detach();
                    var urlString2 = "./request.php?action=getInvoiceID";
                    $.ajax({
                        type: "GET",
                        url: urlString2,
                        cache: false,
                        success: function(data) {
                            var d = new Date();
                            $('#invoiceID').html(data);
                            $('#invoiceNumber').val(data + "/" + d.getMonth() + "/" + d.getFullYear());
                        }
                    });
                } else {
                    $('.toast3').toast('show');
                }
            }
        });
    });
});