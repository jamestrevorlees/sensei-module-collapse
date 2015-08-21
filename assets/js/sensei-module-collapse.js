jQuery(document).ready( function($) {

/**************************************************************/
/* Prepares the cv to be dynamically expandable/collapsible   */
/**************************************************************/
$(function prepareList() {
    //Initially assign all to be collapsed
    $('.expList2').closest('ul')
        .addClass('collapsed')
        .children('ul').hide();

    // Toggle between collapsed/expanded per module
    $('.expList').unbind('click').click(function(event) {
        if(this == event.target) {
            $(this).parentsUntil('.module-lessons').find('.expList2').children('li').toggleClass('expanded');
            $(this).parentsUntil('.module-lessons').find('.expList2').children('li').toggle('medium');
        }
        return false;
    });

    //Hack to add links inside the cv
    $('.expList2 a').unbind('click').click(function() {
        window.open($(this).attr('href'),'_self');
        return false;
    });

    //Create the expand/collapse all button funtionality
    $('.expandList')
        .unbind('click')
        .click( function() {
            $('.collapsed').addClass('expanded');
            $('.collapsed').children('li').show('medium');
        })
    $('.collapseList')
        .unbind('click')
        .click( function() {
            $('.collapsed').removeClass('expanded');
            $('.collapsed').children('li').hide('medium');
        })

})


/**************************************************************/
/* Functions to execute on loading the document               */
/**************************************************************/
document.addEventListener('DOMContentLoaded', function() {
    prepareList();
}, false)

});
