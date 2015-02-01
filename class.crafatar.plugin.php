<?php if (!defined('APPLICATION')) exit();
/**
 * Crafatar Avatars
 * A really simple vanilla forums plugin that will assign a default forum avatar based on a user.
 *
 * @author Chris Ireland
 * @license GNU GPLv2
 */

// Define the plugin:
$PluginInfo['crafatar'] = array(
    'Name' => 'Crafatar Avatars',
    'Description' => 'Crafatar is a blazing fast Minecraft avatar API',
    'Version' => '1.0',
    'RequiredApplications' => array('Vanilla' => '2.0.18'),
    'Author' => 'Chris Ireland',
    'MobileFriendly' => true,
    'SettingsPermission' => 'Garden.Settings.Manage',
    'SettingsUrl' => '/settings/crafatar'
);

class CrafatarPlugin extends Gdn_Plugin
{
    /**
     * Creates a settings page
     *
     * @param $Sender
     */
    public function SettingsController_CrafatarPlugin_Create($Sender)
    {
        $Sender->Permission('Garden.Settings.Manage');
        $Sender->SetData('Title', T('Crafatar Avatar'));
        $Sender->AddSideMenu('dashboard/settings/plugins');

        $Conf = new ConfigurationModule($Sender);
        $Conf->Initialize(array(
            'Plugins.CrafatarPlugin.FallbackUrl' => array(
                'Description' => T('The image to be served when the id has no skin (404)'),
                'Default' => 'steve',
                'LabelCode' => T('Valid options are steve, alex, or a custom URL')
            ),
            'Plugins.CrafatarPlugin.UseHelm' => array(
                'Description' => T('Should avatars include the hat layer?'),
                'Control' => 'CheckBox',
                'LabelCode' => T('Include hat layer'),
                'Default' => '1'
            )
        ));

        $Conf->RenderAll();
    }

    /**
     * Override Profile Pages
     *
     * @param $Sender
     * @param $Args
     */
    public function ProfileController_AfterAddSideMenu_Handler($Sender, $Args)
    {
        if (!$Sender->User->Photo) {
            $username = GetValue('Name', $Sender->User);

            // Helm
            if (C('Plugins.CrafatarPlugin.UseHelm', 1) == 1) {
                $useHelm = true
            }
            
            // Build the query
            $crafatarQuery = array(
                'helm' = $useHelm,
                'default' = C('Plugins.CrafatarPlugin.FallbackUrl', 'steve'); 
            );
            
            $crafatarQuery = http_build_query($crafatarQuery);

            $Sender->User->Photo = 'https://crafatar.com/avatars/' . $username . $crafatarQuery;
        }
    }
}


if (!function_exists('UserPhotoDefaultUrl')) {
    /**
     * Overwrite any other instances
     *
     * @param $User
     * @param array $Options
     * @return string
     */
    function UserPhotoDefaultUrl($User, $Options = array())
    {
        $username = GetValue('Name', $User);

            // Helm
            if (C('Plugins.CrafatarPlugin.UseHelm', 1) == 1) {
                $useHelm = true
            }
            
            // Build the query
            $crafatarQuery = array(
                'helm' = $useHelm,
                'default' = C('Plugins.CrafatarPlugin.FallbackUrl', 'steve'); 
            );
            
            $crafatarQuery = http_build_query($crafatarQuery);
            
            $url = 'https://crafatar.com/avatars/' . $username . $crafatarQuery;

        return $url;
    }
}
