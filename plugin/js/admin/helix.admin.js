/**
* @package Helix Framework
* @author JoomShaper http://www.joomshaper.com
* @copyright Copyright (c) 2010 - 2013 JoomShaper
* @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
*/	
jQuery(function($){

        $('[rel="tooltip"]').tooltip({
                html:true
        });

        // Turn radios into btn-group

        $('.radio.btn-group input[type=radio]').each(function(){
                if( $(this).val()==0 && $(this).closest('label').hasClass('active') ){ 
                    $(this).closest('label').removeClass('btn-success').addClass('btn-danger') 
                }
        });

        $('.radio.btn-group label').addClass('btn');
        $(".btn-group label").click(function() {
                var label = $(this);
                var input = label.find('>input');

                if (!input.prop('checked')) {
                    label.closest('.btn-group').find("label").removeClass('active btn-success btn-danger btn-primary');
                    if(input.val()== '') {
                        label.addClass('active btn-primary');
                    } else if(input.val()==0) {
                        label.addClass('active btn-danger');
                    } else {
                        label.addClass('active btn-success');
                    }
                    input.prop('checked', true);
                }
        });





});