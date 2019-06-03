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

  var $info;
  var $input;
  var showInfo = function(result) {
    $info
      .text(result)
      .css('margin-top', "-"+$info.outerHeight()+"px")
      .fadeIn();
  }
  var hideInfo = function(result) {
    if(result) $input.val(result);
    $info.fadeOut();
  }

  // parse math expression callback
  var parseMathExpression = function(e) {
    var $field = $(e.target).closest('.MathParser');
    $info = $field.find('.info');
    $input = $field.find('input');
  
    // get the value of the inputfield
    var str = $input.val().replace(/,/g,'.');
    if(!str) return hideInfo();
  
    // try to evaluate this expression
    var result;
    var valid = true;
    try {
      result = math.format(math.eval(str), {precision: 14});
      $input.removeClass('MathParserInvalid');
      if(result && !$.isNumeric(result)) throw error;
    } catch (error) {
      result = ProcessWire.config.invalidMathParserExpr;
      $input.addClass('MathParserInvalid');
      valid = false;
    }
  
    if(e.type == 'change') {
      if(result && valid) hideInfo(result);
    }
    else if(e.type == 'focusout') {
      if(valid) hideInfo();
    }
    else {
      if(!result) hideInfo();
      else showInfo(result);
    }
  };

  // listen to events and fire callback
  $(document).on('keyup', '.MathParser input', debounce(parseMathExpression));
  $(document).on('change focus focusout', '.MathParser input', parseMathExpression);
});