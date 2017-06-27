<?php

/* themes/bootstrap/templates/system/image.html.twig */
class __TwigTemplate_e75baf14b506c9ff608277529a20748dec95900750d14267b579a6575b112d4d extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = false;

        $this->blocks = array(
        );
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        $tags = array("set" => 15);
        $filters = array();
        $functions = array();

        try {
            $this->env->getExtension('sandbox')->checkSecurity(
                array('set'),
                array(),
                array()
            );
        } catch (Twig_Sandbox_SecurityError $e) {
            $e->setTemplateFile($this->getTemplateName());

            if ($e instanceof Twig_Sandbox_SecurityNotAllowedTagError && isset($tags[$e->getTagName()])) {
                $e->setTemplateLine($tags[$e->getTagName()]);
            } elseif ($e instanceof Twig_Sandbox_SecurityNotAllowedFilterError && isset($filters[$e->getFilterName()])) {
                $e->setTemplateLine($filters[$e->getFilterName()]);
            } elseif ($e instanceof Twig_Sandbox_SecurityNotAllowedFunctionError && isset($functions[$e->getFunctionName()])) {
                $e->setTemplateLine($functions[$e->getFunctionName()]);
            }

            throw $e;
        }

        // line 15
        $context["classes"] = array(0 => (($this->getAttribute($this->getAttribute(        // line 16
(isset($context["theme"]) ? $context["theme"] : null), "settings", array()), "image_shape", array())) ? ($this->getAttribute($this->getAttribute((isset($context["theme"]) ? $context["theme"] : null), "settings", array()), "image_shape", array())) : ("")), 1 => (($this->getAttribute($this->getAttribute(        // line 17
(isset($context["theme"]) ? $context["theme"] : null), "settings", array()), "image_responsive", array())) ? ("img-responsive") : ("")));
        // line 19
        echo "<img";
        echo $this->env->getExtension('sandbox')->ensureToStringAllowed($this->env->getExtension('drupal_core')->escapeFilter($this->env, $this->getAttribute((isset($context["attributes"]) ? $context["attributes"] : null), "addClass", array(0 => (isset($context["classes"]) ? $context["classes"] : null)), "method"), "html", null, true));
        echo " />
";
    }

    public function getTemplateName()
    {
        return "themes/bootstrap/templates/system/image.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  47 => 19,  45 => 17,  44 => 16,  43 => 15,);
    }

    public function getSource()
    {
        return "{#
/**
 * @file
 * Default theme implementation of an image.
 *
 * Available variables:
 * - attributes: HTML attributes for the img tag.
 * - style_name: (optional) The name of the image style applied.
 *
 * @ingroup templates
 *
 * @see template_preprocess_image()
 */
#}
{% set classes = [
  theme.settings.image_shape ? theme.settings.image_shape,
  theme.settings.image_responsive ? 'img-responsive',
] %}
<img{{ attributes.addClass(classes) }} />
";
    }
}
