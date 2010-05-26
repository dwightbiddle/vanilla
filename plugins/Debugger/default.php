<?php if (!defined('APPLICATION')) exit();
/*
Copyright 2008, 2009 Vanilla Forums Inc.
This file is part of Garden.
Garden is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
Garden is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
You should have received a copy of the GNU General Public License along with Garden.  If not, see <http://www.gnu.org/licenses/>.
Contact Vanilla Forums Inc. at support [at] vanillaforums [dot] com
*/

// Define the plugin:
$PluginInfo['Debugger'] = array(
   'Description' => 'The debugger plugin displays database queries, their benchmarks, and page processing benchmarks at the bottom of each screen of the application.',
   'Version' => '1.0',
   'RequiredApplications' => FALSE, 
   'RequiredTheme' => FALSE, 
   'RequiredPlugins' => FALSE, // This is an array of plugin names/versions that this plugin requires
   'HasLocale' => FALSE, // Does this plugin have any locale definitions?
   'RegisterPermissions' => array('Plugins.Debugger.View','Plugins.Debugger.Manage'), // Permissions that should be added to the application. These will be prefixed with "Plugins.PluginName."
   'SettingsUrl' => '/dashboard/plugin/debugger', // Url of the plugin's settings page.
   'SettingsPermission' => 'Plugins.Debugger.Manage', // The permission required to view the SettingsUrl.
   'PluginUrl' => 'http://vanillaforums.org/addons/debugger',
   'Author' => "Mark O'Sullivan",
   'AuthorEmail' => 'mark@vanillaforums.com',
   'AuthorUrl' => 'http://markosullivan.ca'
);

// Install the debugger database.
$tmp = Gdn::FactoryOverwrite(TRUE);
Gdn::FactoryInstall(Gdn::AliasDatabase, 'Gdn_DatabaseDebug', dirname(__FILE__).DS.'class.database.debug.php', Gdn::FactorySingleton, array('Database'));
Gdn::FactoryOverwrite($tmp);
unset($tmp);

class DebuggerPlugin extends Gdn_Plugin {
   // Specifying "Base" as the class name allows us to make the method get called for every
   // class that implements a base class's method. For example, Base_Render_After
   // would allow all controllers that call Controller.Render() to have that method
   // be called. It saves you from having to go:
   // Table_Render_After, Row_Render_After, Item_Render_After,
   // SignIn_Render_After, etc. and it essentially *_Render_After
   
   public function Base_Render_Before($Sender) {
      $Sender->AddCssFile('/plugins/Debugger/style.css');
   }

   public function Base_AfterBody_Handler($Sender) {
      $Session = Gdn::Session();
      if(!$Session->CheckPermission('Garden.Settings.Manage')) {
         return;
      }
      
      if (!$Sender->Head)
         $Sender->Head = new HeadModule($Sender);
         
      $Sender->Head->AddCss('/plugins/Debugger/style.css');
      //$Session = Gdn::Session();
      //if ($Session->CheckPermission('Plugins.Debugger.View')) {
         $String = '<div id="Sql">';
         $Database = Gdn::Database();
         if(!is_null($Database)) {
            $Queries = $Database->Queries();
            $QueryTimes = $Database->QueryTimes();
            $String .= '<h3>'.count($Queries).' queries in '.$Database->ExecutionTime().'s</h3>';
            foreach ($Queries as $Key => $QueryInfo) {
               $Query = $QueryInfo['Sql'];
               // this is a bit of a kludge. I found that the regex below would mess up when there were incremented named parameters. Ie. it would replace :Param before :Param0, which ended up with some values like "'4'0".
               if(isset($QueryInfo['Parameters']) && is_array($QueryInfo['Parameters'])) {
                  $tmp = $QueryInfo['Parameters'];
                  arsort($tmp);
                  foreach ($tmp as $Name => $Parameter) {
                     $Pattern = '/(.+)('.$Name.')([\W\s]*)(.*)/';
                     $Replacement = "$1'".htmlentities($Parameter, ENT_COMPAT, 'UTF-8')."'$3$4";
                     $Query = preg_replace($Pattern, $Replacement, $Query);
                  }
               }
               $String .= $QueryInfo['Method']
                  .'<small>'.@number_format($QueryTimes[$Key], 6).'s</small>'
                  .'<pre>'.$Query.';</pre>';
            }
         }
         global $Start;
         $String .= '<h3>Page completed in '.round(Now() - $_SERVER['REQUEST_TIME'], 4).'s</h3>';
            /*
            <div>
               <strong>Application:</strong> ' . $Sender->ApplicationFolder . ';
               <strong>Controller:</strong> ' . $Sender->ClassName . ';
               <strong>Method:</strong> ' . $Sender->RequestMethod . ';
            </div>
         </div>';
              */
         $String .= '</div>';
         echo $String;
      //}
   }
   
   public function PluginController_Debugger_Create($Sender) {
      $Sender->Render();
   }
   
   public function Setup() {
      // This setup method should trigger errors when it encounters them - the plugin manager will catch the errors...
   }
}