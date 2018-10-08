$(document).ready(function() {
  
  // debounce function
  var debounce = function(func, wait, immediate) {
    var wait = wait || 500; // 500ms default
    var timeout;
    return function() {
      var context = this, args = arguments;
      var later = function() {
        timeout = null;
        if (!immediate) func.apply(context, args);
      };
      var callNow = immediate && !timeout;
      clearTimeout(timeout);
      timeout = setTimeout(later, wait);
      if(callNow) func.apply(context, args);
    };
  };

  // parse math expression callback
  var parseMathExpression = function(e) {
    var $field = $(e.target).closest('.MathParser');
    var $info = $field.find('.info');
    var $input = $field.find('input');
  
    // get the value of the inputfield
    var str = $input.val().replace(',','.');
  
    // try to evaluate this expression
    var result;
    var valid = true;
    try {
      result = parseFloat(math.eval(str));
      $input.removeClass('MathParserInvalid');
      if(result && !$.isNumeric(result)) throw error;
    } catch (error) {
      result = ProcessWire.config.invalidMathParserExpr;
      $input.addClass('MathParserInvalid');
      valid = false;
    }
  
    if(e.type == 'change') {
      if(result && valid) {
        $input.val(result);
        $info.fadeOut();
      }
    }
    else if(e.type == 'focusout') {
      if(valid) $info.fadeOut();
    }
    else {
      if(!result) $info.fadeOut();
      else {
        // write the result to the info span
        $info
          .text(result)
          .css('margin-top', "-"+$info.outerHeight()+"px")
          .fadeIn();
      }
    }
  };

  // listen to events and fire callback
  $(document).on('keyup', '.MathParser input', debounce(parseMathExpression));
  $(document).on('change focus focusout', '.MathParser input', parseMathExpression);
});