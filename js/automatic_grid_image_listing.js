jQuery(document).ready(function(){
	


});

function agil_validate(){//alert('sds');
	var numbers = /^[0-9]+$/;  
	var folder_name = jQuery('#agil_base_folder_name').val();
	var agil_border_width = jQuery('#agil_border_width').val();
	var agil_grid_width = jQuery('#agil_grid_width').val();
	var agil_grid_height = jQuery('#agil_grid_height').val();
	//alert('jhj');
	if(folder_name == '' && folder_name == null && folder_name == undefined){
		alert('please enter Valid Folder Name');
		return false;
	}	
	if (/\s/.test(folder_name)) {
    alert('please enter Valid Folder Name');
		return false;
	}
	if(!agil_border_width.match(numbers))  
      {  
      alert('Please Enter Numeric value only.');      
      return false;  
      }  
	if(!agil_grid_width.match(numbers))  
      {  
      alert('Please Enter Numeric value only in Width');      
      return false;  
      }  
	if(!agil_grid_height.match(numbers))  
      {  
      alert('Please Enter Numeric value only in height');      
      return false;  
      }  
return true;
}
