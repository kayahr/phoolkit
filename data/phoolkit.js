/**
 * The phoolkit namespace.
 * @type {Object}
 */
var phoolkit = {};

phoolkit.Form = function(form)
{
    this.form = form;
};

/**
 * The form element.
 * @private
 * @type {Element}
 */
phoolkit.Form.prototype.form = null;

/**
 * If form is valid or not.
 * @private
 * @type {boolean}
 */
phoolkit.Form.prototype.valid = true;

phoolkit.Form.prototype.submit = function(validations)
{
    var $form = $(this.form);
    if ($form.hasClass("submitting")) return false;
    if (!this.validate(validations)) return false;
    $form.addClass("submitting");
    return true;
};

/**
 * Validates the form with the specified validations.
 *
 * @param {Function} validations
 *            The validations to run.
 */
phoolkit.Form.prototype.validate = function(validations)
{
    this.valid = true;
    try
    {
        $("ul.errors", this.form).html("");
        validations.call(this);
    }
    catch (e)
    {
        // When an exception occurs during validation then rethrow this
        // exception asynchronously so we still can return false here to
        // prevent form submission.
        setTimeout(function() { throw e; }, 0);
        this.valid = false;
    }
    return this.valid;
};

/**
 * Reports an error.
 *
 * @param {string} field
 *            The field for which the error is reported.
 * @param {string} message
 *            The reported error message.
 * @protected
 */
phoolkit.Form.prototype.error = function(field, message)
{
    $("ul.errors." + field, this.form).append("<li>" + message + "</li>");
    if (this.valid) this.form[field].focus();
    this.valid = false;
};

/**
 * Returns the current value of the specified field.
 *
 * @param {string} field
 *            The field name.
 * @return {string} value
 *            The field value.
 */
phoolkit.Form.prototype.get = function(field)
{
    return this.form[field].value;
};

// Add some special behavious
$(function()
{
    // Implement autofocus for older browsers
    if (!("autofocus" in document.createElement("input")))
    {
        $("*[autofocus]").each(function(index, value)
        {
            value.focus();
        });
    }
});