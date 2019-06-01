<?php

// TO-Do
// Add some more error msg

// Some Notes:
/**
 * Private: Methods or properties which have the visibility "private" can only be changed within this class.
 * Public: Methods or properties which have the visibility "public" can be called anywhere using the object.
 * Example: $variable = new tpl() -> Will generate a new Object of tpl class. 
 *          After you have an object you can call its public methods. 
 *                   $object    method within the class
 *          Example: $variable->output() 
 * Protected: "protected" works the same as "private" with one key difference: 
 *            inherited classes can invoke and / or modify these methods or properties
 * Static:
 */

class tpl {
 // ################ Configurations ######################
    // The folder in which the templates are located
    private $tplDir = "../app/views/";
    // The variable for the complete path of the template file.
    private $tplFile = "";
    // The variable for the name of the template file.
    private $tplName = "";
    // File type .html .tpl etc
    private $tplType = ".php";
    // Delimiter for standard placeholder example: {$key}
    private $lDel = '{$'; // left
    private $rDel = '}';  // right  
    // The variable for content of the template.
    private $template = "";
 // ################ End of Configurations #################
    protected $values = array();
    // ################ Constructor ######################
    /** Constructor
     *
     *  @access    public
     *  @param     string $tpl_name  -> Name of the tpl File
     *  @uses      $tplFile
     *  @uses      $tplDir
     *  @uses      $tplType
     * 
     *  With this constructor you will set the full template file/path
     *  For Example: 
     *  Create a new Object of tpl: $tpl = new tpl("users"); 
     *  -> "users" will be the name of the template file 
     *  $this->tplFile will be: "public/tpl/users.tpl
    */ 
    public function __construct($tpl_name) {
        $this->tplFile = $this->tplDir.$tpl_name.$this->tplType;
        // this will call the method open and will send the template file/path as parameter
        $this->open($this->tplFile);
    }
    // ################ Load template file ######################
    /** Load template file
     * 
     * @access    private
     * @param     string $file  -> name/path of the template
     * @uses      $template
     * 
     * This method tries to open a template. If the file was successfully opened, 
     * the content of the template is saved in the variable $template.
     * $file = $this->tplFile from the constructor
     * 
     */
    private function open($file) {
        // check, if the file exists   
        if (!file_exists($file)) {
            // if this file wont exist return an error message
			return "This template couldn't be loaded!! Please check if this file exists under the following path! ($file).";
        }
        // if the file exists, save all the content into the $template variable
        $this->template = file_get_contents($file);
    }
    // ################ Assign values for your placeholder ######################
    /** Assign values for your placeholder
     * 
     * @access    public
     * @param     string $key     -> a placeholder within the template
     * @param     string $value   -> the content with which the placeholder is to be replaced
     * @uses      $values
     * 
     * With this method you will assign your replacements for your placeholders within your template
     * For example: 
     * The placeholder in your template is {$username}
     *                              $key        $value
     * So you can write: $tpl->assign("usersname", "Janina");
     * Then instead of $username there will be shown Janina
     * in your output. 
     * 
     */
    public function assign($key, $value) {
        // Save all replacements into an array
        $this->values[$key] = $value;
    }
    // ################ Final replacements ######################
    /** Final replacements
     * 
     * @access    public
     * @uses      $values
     * @uses      $lDel
     * @uses      $rDel
     * @uses      $template
     * @return    boolean
     * 
     * This method scans the entire file for placeholders and replaces them with the assigned values
     * 
     */
    public function replace() {
		foreach ($this->values as $key => $value) {
            // => $this->$lDel = {$   // can be changed in the settings above
            // => $key         = the placeholdername for example "username"
            // => $this->$lDel = }    // can be changed in the settings above
            // => $tagToReplace {$username}
            $wholeKey = $this->lDel.$key.$this->rDel;
            // Search the template for the entire key and replace it with the desired 
            // content and saves it again in the template variable
			$this->template = str_replace($wholeKey, $value, $this->template);
        }
        // returns the template with all replacements
        return $this->template;
    }
    // ################ Combine templates with each other. ######################
    /** Combine templates with each other.
     * 
     * @access    public
     * @param     string $templates -> Saved array with for example assigned database content
     * @return    boolean
     * 
     * Important for multiple output of the same template file with different values.
     * Especially for database rows
     * Use: $tpl->replace();
     * 
     */
    static public function merge($templates, $separator = "") {
        // set an variable to place the content
        $output = "";
        // uses the template and add it for each
            foreach ($templates as $template) {
                $content = (get_class($template) !== "tpl")
                    ? "Error, incorrect type - expected Template."
                    : $template->replace(); // replace the placeholders
                $output .= $content. $separator; // save it
            }
        // return it
        return $output;
        }
    // ################ Finally show everything ######################
    /** Finally show everything
     * 
     * @access    public
     * @uses      $template
     * 
     * This will output the whole "new" template with all replacements
     * 
     */
     public function show(){
        echo $this->template;
     }   
}
?>