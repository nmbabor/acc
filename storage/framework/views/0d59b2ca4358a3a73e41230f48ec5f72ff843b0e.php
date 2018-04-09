<script src="<?php echo e(asset('public/js/chosen.jquery.js')); ?>" type="text/javascript"></script> 

<script type="text/javascript">
// disable mousewheel on a input number field when in focus
// (to prevent Cromium browsers change the value when scrolling)
$('form').on('focus', 'input[type=number]', function (e) {
  $(this).on('mousewheel.disableScroll', function (e) {
    e.preventDefault()
  })
})
$('form').on('blur', 'input[type=number]', function (e) {
  $(this).off('mousewheel.disableScroll')
})
var path = $('#rootUrl').val();
     /**
     * Site : http:www.smarttutorials.net
    //  * @author  muni
    //  */
              
     //adds extra table rows
     var i=$('#table_auto tr').length;
     $(".addmore").on('click',function(){
        html = '<tr>';
        html += '<td><select id="sector_'+i+'" name="fk_sub_category_id[]" data-placeholder="- Select -" class="select form-control sectorSelect" tabindex="10" required="required"><option value="">Please choose</option><?php foreach ($subCategories as $subCategory){ ?><option value="<?php echo $subCategory->id; ?>"><?php echo $subCategory->sub_category_name; ?></option><?php } ?></select></td>';
        html +='<td><input  id="asset_'+i+'" type="number" min="0" step="any" name="asset_age[]" readonly placeholder="Asset age" class="form-control"><input type="hidden" name="asset_type_id[]" id="asset_type_'+i+'"></td>';
        html += '<td><input type="text" data-type="description" name="description[]" id="description_'+i+'" class="form-control autocomplete_txt" autocomplete="off"></td>';

        html += '<td><input type="number" step="any" min="0" name="total[]" id="price_'+i+'" class="form-control onChangeAmount" onKeyPress="amount(this.value,'+i+')" onKeyUp="amount(this.value,'+i+')"></td>';

        html += '<td><input type="number" step="any" min="0" name="paid[]" id="paid_'+i+'" class="form-control onChangeAmountPaid" autocomplete="off"></td>';
        html +='<td><button type="button" class="btn btn-danger btn-xs deleteBtn"> <i class="fa fa-trash"></i> </button></td>';
        html += '</tr>';
        $('#table_auto').append(html);
        i++;
        $('.select').chosen("liszt:updated");

     });
     

     //deletes the selected table rows
     $(document).on('click','.deleteBtn',function(){

        $(this).parents("tr").remove();
         calculateTotal();
     });
     //total amount calculate
     $(document).on('change keyup blur','.onChangeAmount',function(){
         calculateTotal();
     });
     function calculateTotal(){
         total = 0;
         //$('#testField').val(123);
         $('.onChangeAmount').each(function(){

             //alert(total);
             if($(this).val() != '' )total += parseFloat( $(this).val() );
             $("#total_amount").val(total);

             $("#total-due-amount").val(total);
         });
         calculateTotalPaid();
     }

     //total amount paid calculate
     $(document).on('change keyup blur','.onChangeAmountPaid',function(){
         calculateTotalPaid();
     });
     function calculateTotalPaid(){
         totalPaid = 0;
         $('.onChangeAmountPaid').each(function(){

             //alert(total);
             if($(this).val() != '' )totalPaid += parseFloat( $(this).val() );
             $("#amountPaid").val(totalPaid);
             //check due
             amount_due = parseFloat($("#total_amount").val()) - parseFloat( $("#amountPaid").val());
             $("#total-due-amount").val(amount_due);
         });

     }
     function amount($value,$id){
        $('#paid_'+$id).attr('max',$value);
     }
     var path=$('#rootUrl').val();
     function autoComplete(id){
        $('#'+id).autocomplete({
            source: function( request, response ) {
                $.ajax({
                    //url: "<?php echo e(URL::to('inventory-product-search')); ?>",
                    url: path+'/payment-client',
                    type: "GET",
                    dataType: "json",
                    data: {
                        name: request.term,
                        },
                    success: function( data ) {
                        //console.log(data);
                         response( $.map( data, function( item ) {
                            var code = item.split("|");
                            return {
                                label: code[0],
                                value: code[0],
                                data:item
                            }
                        }));
                    }
                });

                
            },

            autoFocus: true ,
            minLength: 0,
            select: function( event, ui ) {
                var names = ui.item.data.split("|");
                var id = $(this).attr('id');
                $('#'+id+'_h').val(names[1]);
            }       
        });

    }
$(document).on('change blur','.sectorSelect',function(){
    var id = $(this).attr('id').split('_')[1];
    var value = $(this).val();
    var bank = $('#client_h').val();
    console.log(bank);
    $.ajax({
        url:path+'/subcategory-asset-type/'+value,
        type:'GET',
        success: function(result){
            if(result.asset_type===1){
                $('#loadBank').html('');
                if(id==1){

                $('#loanId_'+id).html('<input type="number" min="0" class="form-control" id="asset_1" placeholder="Asset Age" name="asset_age"> ');
                }
                $('#asset_'+id).attr('readonly',false);
                $('#loanIdLabel').html('Asset Age');
                $('.addmore').fadeIn();
            }else if(result.asset_type===3){
                $('#loadBank').html('');
                $('#loanIdLabel').html('Loan ID');
                $('#loanId_1').load('<?php echo e(URL::to("payable-loan-id")); ?>?bank='+bank);
                $('.addmore').fadeOut();
            }else if(result.asset_type===2){
                $('.addmore').fadeOut();

            } else{
                $('#loadBank').html('');
                $('#loanIdLabel').html('Asset Age');
                if(id==1){
                $('#loanId_'+id).html('<input type="number" min="0" class="form-control" id="asset_1" placeholder="Asset Age" readonly name="asset_age"> ');   
                }
                $('#asset_'+id).attr('readonly',true);
                $('#asset_'+id).val('');
                $('.addmore').fadeIn();
            }
            $('#asset_type_'+id).val(result.asset_type_id);

        }
    })
 });
$(document).on('change','#loan_id_list',function(){
    var id = $(this).val();
    if(id.length>0){
        $.ajax({
            url:path+'/payable-loan-check/'+id,
            type:"GET",
            success:function(result){
                if(result[0]!=''){
                    if(result[1]==null){
                        result[1] = 0;
                    }
                $('#loadBank').html('<p><b> Total Amount : </b> '+result[0]+'<br> <b>Total Paid : </b> '+result[1]+'<br><b>Due :</b>'+(parseFloat(result[0])-parseFloat(result[1]))+' </p>');
                }else{
                   $('#loadBank').html('<p><b class="text-danger">Loan Not Found!</b>'); 
                }
            }

        });
    }
});

</script>