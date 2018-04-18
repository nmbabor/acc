
	      
//adds extra table rows
var i=$('#table_auto tr').length;
$(".addmore").on('click',function(){
	html = '<tr>';
	html += '<td><input class="case" type="checkbox"/></td>';

	html += '<td><input type="text" data-type="product_name" name="product_name[]" id="itemName_'+i+'" class="form-control autocomplete_txt" autocomplete="off"><input type="hidden" data-type="product_name" name="fk_product_id[]" id="itemId_'+i+'" class="form-control autocomplete_txt" autocomplete="off"></td>';

	html += '<td><input type="text" min="0" name="product_price_amount[]" step="any" id="hidden_'+i+'" class="form-control changesNo" autocomplete="off" onkeypress="return IsNumeric(event);" ondrop="return false;" onpaste="return false;" readonly="readonly"></td>';
	html+='<td><input type="number" min="0" step="any" name="qty[]" id="qty_'+i+'" class="form-control changesNo qty" autocomplete="off" onkeypress="return IsNumeric(event);" ondrop="return false;" onpaste="return false;"></td>';

	html += '<td><input type="number" min="0" step="any" name="product_wise_discount[]" id="benefit_'+i+'" class="form-control changesNo benefit" autocomplete="off" onkeypress="return IsNumeric(event);" ondrop="return false;" placeholder="Discount" onpaste="return false;"></td>';

	html += '<td><input type="text" min="0" step="any" name="product_paid_amount[]" id="total_'+i+'" class="form-control totalLinePrice" autocomplete="off" onkeypress="return IsNumeric(event);" ondrop="return false;" onpaste="return false;"readonly="readonly"><input type="hidden" step="any" min="0" name="price[]" id="cost_'+i+'" class="form-control changesNo" autocomplete="off" onkeypress="return IsNumeric(event);" ondrop="return false;" onpaste="return false;" ></td>';

	html += '</tr>';
	$('#table_auto').append(html);
	i++;
});

//to check all checkboxes
$(document).on('change','#check_all',function(){
	$('input[class=case]:checkbox').prop("checked", $(this).is(':checked'));
});

//deletes the selected table rows
$(".delete").on('click', function() {
	$('.case:checkbox:checked').parents("tr").remove();
	$('#check_all').prop("checked", false); 
	calculateTotal();
});

//autocomplete script
$(document).on('focus','.autocomplete_txt',function(){
	type = $(this).data('type');
	if(type =='product_name' )autoTypeNo=1; 	
	

	$(this).autocomplete({
		source: function( request, response ) {

            _token = $('input[name="_token"]').val();
            $.ajax({
                //url: "{{URL::to('inventory-product-search')}}",
                url: '../service-search',
                type: "GET",
                dataType: "json",
                data: { _token : _token,
                    name_startsWith: request.term,
                    type: type,
                    },
                success: function( data ) {
					 response( $.map( data, function( item ) {
					 	var code = item.split("|");
						return {
							label: code[autoTypeNo],
							value: code[autoTypeNo],
							data : item
						}
					}));
				}
            });

			
		},

		autoFocus: true,	      	
		minLength: 0,
		appendTo: "#modal-fullscreen",
		select: function( event, ui ) {
			var names = ui.item.data.split("|");
			id_arr = $(this).attr('id');
	  		id = id_arr.split("_");
	  		if(names[7]>0){
	  			smallQtyV=names[7]-1;
	  		}else{
	  			smallQtyV=0;
	  		}
			$('#qty_'+id[1]).val(1);
			$('#itemId_'+id[1]).val(names[0]);
			$('#itemName_'+id[1]).val(names[1]);
			$('#cost_'+id[1]).val(names[2]);
			$('#hidden_'+id[1]).val(names[2]);
			$("#benefit_"+id[1]).val(0)
			$('#total_'+id[1]).val( 1*names[2] );

			calculateTotal();
		}		      	
	});
});
$(document).on('change keyup blur','.changesNo',function(){
	id_arr = $(this).attr('id');
	id = id_arr.split("_");
    var quantity = $('#qty_'+id[1]).val();
    var price = $('#hidden_'+id[1]).val();
    var benefit = $("#benefit_"+id).val() || 0;
    var amount = parseFloat(price)*parseFloat(quantity);
    var salesAmount = parseFloat(amount)-parseFloat(benefit);
    if( quantity!='' && price !='') $('#total_'+id[1]).val(parseFloat(salesAmount));

    calculateTotal();
    calculateBenefit(id[1]);
});
$(document).on('change keyup blur','.changesNo',function(){
	calculateTotal();
});

//total price calculation 
function calculateTotal(){
	subTotal = 0 ; total = 0; 
	$('.totalLinePrice').each(function(){
		if($(this).val() != '' )subTotal += parseFloat( $(this).val() );
	});
	$('#subTotal').val( subTotal );
    calculateAmountDiscount();
}



$(document).on('change keyup blur','.benefit',function(){
    id_arr = $(this).attr('id');
    id = id_arr.split("_")[1];
	calculateBenefit(id);
});

//due amount calculation
function calculateBenefit(id){
	priceChange = $('.totalLinePrice').val();
	benefit = $("#benefit_"+id).val() || 0;
	
	price = $("#hidden_"+id).val();
	var amount = parseFloat(price)*parseFloat($("#qty_"+id).val());
    salesAmount = parseFloat(amount)-parseFloat(benefit);

    $("#total_"+id).val(salesAmount);
    calculateTotal();

}

$(document).on('change keyup blur','#amountPaid1',function(){
	calculateAmountDiscount();
});

//due amount calculation
function calculateAmountDiscount(){
	amountPaid1 = $('#amountPaid1').val();
	total = $('#subTotal').val();
	console.log(total);
	
	amountDue = parseFloat(total) - parseFloat( amountPaid1 );
	$('#amountDue').val( amountDue);
//$('#totalAftertax').val( total );
	calculateAmountDue();
}

$(document).on('change keyup blur','#amountPaid',function(){
	calculateAmountDue();
});

//due amount calculation
function calculateAmountDue(){
	total = $('#subTotal').val();
	amountPaid = $('#amountPaid').val();
	
	//amountPaid1 = $('#amountPaid1').val();
	if(amountPaid != '' && typeof(amountPaid) != "undefined" ){
		amountDue = parseFloat(total) - parseFloat( amountPaid );
		$('.amountDue').val( amountDue );
	}else{
		total = parseFloat(total);
		$('.amountDue').val( total);
	}

	//calculatePaid();
}



//It restrict the non-numbers
var specialKeys = new Array();
specialKeys.push(8,46); //Backspace
function IsNumeric(e) {
    var keyCode = e.which ? e.which : e.keyCode;
    console.log( keyCode );
    var ret = ((keyCode >= 48 && keyCode <= 57) || specialKeys.indexOf(keyCode) != -1);
    return ret;
}
