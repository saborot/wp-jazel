var $ = jQuery.noConflict();

var jazelCalc = {
    
    init: function() {
        var that = this;
        
        $(document).on('click','.jazel-calc .button-calculate',function() {
            
            var calc = $(this).parents('.jazel-calc');
            if (that.validateCalcFields(calc)) {
                that.calculateMonthly(calc);
            }
        });
        
        $(document).on('click','.jazel-calc .view-inventory',function() {

            var price = $('.jazel-calc').find("input[name='price']").val() || 0;
            var inv_url = $(this).data('url');
            window.location.assign(inv_url + '#/price:' + encodeURIComponent(price));
        });
        
        $(document).on('keyup','.jazel-calc input[data-format="monetary"]', function() {
            
            var value = $(this).val();
            if ( value.charAt(0) != '$' ) {
                $(this).val( '$' + $(this).val() );
            }
        });
        
        $(document).on('keyup','.jazel-calc input[data-format="percentage"]', function() {
            
            var value = $(this).val();
            var index = value.indexOf('%');
            
            if ( index != value.length ) {
                
                $(this).val($(this).val().replace('%',''));
            }
            
            $(this).val( $(this).val() + '%' );
            this.setSelectionRange(value.length-1,value.length-1);
        });        
    },
    calculateMonthly: function(calculator) {

        var price = calculator.find("input[name='price']");
        price = this.parseValue(price.val(), price.data('format')) || 0;
        
        var downpayment = calculator.find("input[name='downpayment']");
        downpayment = this.parseValue(downpayment.val(), downpayment.data('format')) || 0;
        
        var monthlyPayment = calculator.find("input[name='monthlypayment']");
        monthlyPayment = this.parseValue(monthlyPayment.val(), monthlyPayment.data('format')) || 0;
        
        var apr = calculator.find("input[name='apr']");
        apr = this.parseValue(apr.val(), apr.data('format')) || 0;
        
        var term = calculator.find("input[name='term']").val() || 0;
        
        var tradein = calculator.find("input[name='tradein']");
        tradein = this.parseValue(tradein.val(), tradein.data('format')) || 0;
        
        var payoff = calculator.find("input[name='payoff']");
        payoff = this.parseValue(payoff.val(), payoff.data('format')) || 0;
        
        var resultContainer = calculator.find(".results .estimate span");

        var monthlyRate = apr / (12 * 100);
        var base = 1 + monthlyRate;
        var exponent = -1 * term;

        var result = 0;
        if (monthlyPayment == 0) {
            price -= downpayment;
            price -= tradein - payoff;
            result = price * (monthlyRate / (1 - Math.pow(base, exponent)));
        } else {
            result = monthlyPayment / (monthlyRate / (1 - Math.pow(base, exponent)));
            result += Number(downpayment);
            result += tradein - payoff;
        }

        resultContainer.html('$' + result.toFixed(0));
        calculator.find(".results").slideDown('fast');
    },
    validateCalcFields: function(calculator) {
        var validated = true;
        var messages = {
            "default": "Please enter a number.",
            "APR": "Please enter a value between 0.001 and 99."
        };
        var inputs = $(calculator).find("input[type='text'],input[type='number']");
        calculator.find("label").css("color", "inherit");
        inputs.removeClass("error");
        calculator.find(".reqMsg").remove();
        inputs.each(function () {
            
            var value = $(this).val();
            
            switch($(this).data('format')) {
                case 'monetary':
                    value = $(this).val().substring(1,value.length);
                    break;
                case 'percentage':
                    value = $(this).val().substring(0,value.length-1);
                    break;
                default:
            }
    
            var name = $(this).attr("name");
            var reqButNoValue = value.length <= 0 && $(this).hasClass("req");
            var minAprValue = name === "apr" && value < 0.001;
            if (isNaN(value) || reqButNoValue || minAprValue) {
                $(this).addClass('error');
                $(this).prev("label").css("color", "#B00")
                $(this).find(".reqLabel").addClass("invalid error");
                if (name === "apr") {
                    errMsg = messages.APR;
                } else {
                    errMsg = messages['default'];
                }
                $(this).prev("label").append("<span class='reqMsg' style='display:block;opacity:0;'>" + errMsg + "</div>");
                $('.reqMsg').animate({
                    opacity: 1
                }, 500);

                validated = false;
            }
        });

        return validated;
    },
    parseValue: function(value, format) {

        switch(format) {
            case 'monetary':
                value = value.substring(1, value.length)
                break;
            case 'percentage':
                value = value.substring(0, value.length-1)
                break;
            default:
        }

        return value;
    }
};

$(function(){

    jazelCalc.init();

});