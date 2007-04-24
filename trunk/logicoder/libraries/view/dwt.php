<?php
/**
 * Logicoder Web Application Framework - Dreamweaver Template View
 *
 * @package     Logicoder
 * @copyright   Copyright (c) 1999-2007, Marco Del Tongo
 * @license     http://logicoder.com/documentation/license.html
 * @version     $Id$
 */

// -----------------------------------------------------------------------------

/**
 * Dreamweaver Template View class.
 *
 * @package     Logicoder
 * @subpackage  Views
 * @link        http://www.logicoder.com/documentation/views.html
 * @author      Marco Del Tongo <info@marcodeltongo.com>
 */
class Logicoder_View_DWT extends Logicoder_View_Abstract
{
    /**
     * Parse source template.
     */
    public function _parse ( /* void */ )
    {
        ob_start();
        /*
            TO DO !
        */
        $this->sParsed = ob_get_clean();
    }
}
// END Logicoder_View_DWT class

// -----------------------------------------------------------------------------

/*
    = Template tags =

    Dreamweaver uses the following template tags:

    <!-- TemplateBeginEditable name="..." -->
    <!-- TemplateEndEditable -->

    <!-- TemplateParam name="..." type="..." value="..." -->

    <!-- TemplateBeginRepeat name="..." -->
    <!-- TemplateEndRepeat -->

    <!-- TemplateBeginIf cond="..." -->
    <!-- TemplateEndIf -->

    <!-- TemplateBeginPassthroughIf cond="..." -->
    <!-- TemplateEndPassthroughIf -->

    <!-- TemplateBeginMultipleIf -->
    <!-- TemplateEndMultipleIf -->

    <!-- TemplateBeginPassthroughMultipleIf -->
    <!-- TemplateEndPassthroughMultipleIf -->

    <!-- TemplateBeginIfClause cond="..." -->
    <!-- TemplateEndIfClause -->

    <!-- TemplateBeginPassthroughIfClause cond="..." -->
    <!-- TemplateEndPassthroughIfClause -->

    <!-- TemplateExpr expr="..." --> (equivalent to @@...@@)

    <!-- TemplatePassthroughExpr expr="..." -->

    <!-- TemplateInfo codeOutsideHTMLIsLocked="..." -->



    = Instance tags =

    Dreamweaver uses the following instance tags:

    <!-- InstanceBegin template="..." codeOutsideHTMLIsLocked="..." -->
    <!-- InstanceEnd -->
    <!-- InstanceBeginEditable name="..." -->
    <!-- InstanceEndEditable -->
    <!-- InstanceParam name="..." type="..." value="..." passthrough="..." -->
    <!-- InstanceBeginRepeat name="..." -->
    <!-- InstanceEndRepeat -->
    <!-- InstanceBeginRepeatEntry -->
    <!-- InstanceEndRepeatEntry -->



    = The template expression language =

    The template expression language is a small subset of JavaScript, and uses JavaScript syntax and precedence rules. You can use JavaScript operators to write an expression like this:

    @@(firstName+lastName)@@

    The following features and operators are supported:

        * numeric literals, string literals (double-quote syntax only), Boolean literals (true or false)
        * variable reference (see the list of defined variables later in this section)
        * field reference (the "dot" operator)
        * unary operators: +, -, ~, !
        * binary operators: +, -, *, /, %, &, |, ^, &&, ||, <, <=, >, >=, ==, !=, <<, >>
        * conditional operator: ?:
        * parentheses: ()

    The following data types are used: Boolean, IEEE 64-bit floating point, string, and object. Dreamweaver templates do not support the use of JavaScript "null" or "undefined" types. Nor do they allow scalar types to be implicitly converted into an object; thus, the expression "abc".length would trigger an error, instead of yielding the value 3.

    The only objects available are those defined by the expression object model. The following variables are defined:
    _document

    Contains the document-level template data with a field for each parameter in the template.
    _repeat

    Only defined for expressions which appear inside a repeating region. Provides built-in information about the region:

    _index The numerical index (from 0) of the current entry

    _numRows The total number of entries in this repeating region

    _isFirst True if the current entry is the first entry in its repeating region

    _isLast True if the current entry is the last entry in its repeating region

    _prevRecord The _repeat object for the previous entry. It is an error to access this property for the first entry in the region.

    _nextRecord The _repeat object for the next entry. It is an error to access this property for the last entry in the region.

    _parent In a nested repeated region, this gives the _repeat object for the enclosing (outer) repeated region. It is an error to access this property outside of a nested repeated region.

    During expression evaluation, all fields of the _document object and _repeat object are implicitly available. For example, you can enter title instead of _document.title to access the document's title parameter.

    In cases where there is a field conflict, fields of the _repeat object take precedence over fields of the _document object. Therefore, you shouldn't need to explicitly reference _document or _repeat except that _document might be needed inside a repeat region to reference document parameters that are hidden by repeated region parameters.

    When nested repeated regions are used, only fields of the innermost repeated regions are available implicitly. Outer regions must be explicitly referenced using _parent.



    = The Multiple If condition in template code =

    You can define template expressions for if and multiple-if conditions (see Template expressions). This example demonstrates defining a parameter named "Dept", setting an initial value, and defining a Multiple If condition which determines which logo to display.

    The following is an example of the code you might enter in the head section of the template:

    <!-- TemplateParam name="Dept" type="number" value="1" -->

    The following condition statement checks the value assigned to the Dept parameter. When the condition is true or matches, the appropriate image is displayed.

    <!-- TemplateBeginMultipleIf -->
    <!-- checks value of Dept and shows appropriate image-->

    <!-- TemplateBeginClause cond="Dept == 1" --> <img src=".../sales.gif">
    <!-- TemplateEndIfClause -->
    <!-- TemplateBeginIfClause cond="Dept == 2" --> <img src=".../
    support.gif"> <!-- TemplateEndIfClause-->
    <!-- TemplateBeginIfClause cond="Dept == 3" --> <img src=".../hr.gif">
    <!-- TemplateEndIfClause -->
    <!-- TemplateBeginIfClause cond="Dept != 3" --> <img src=".../
    spacer.gif"> <!-- TemplateEndIfClause -->


    <!-- TemplateEndMultipleIf -->

    When you create a template-based document, the template parameters are automatically passed to it. The template user determines which image to display
*/
