<?php

/**
 * @author Matthew McNaney <mcnaney at gmail dot com>
 * @version $Id$
 */

function comments_update(&$content, $currentVersion)
{
    switch ($currentVersion) {

    case version_compare($currentVersion, '0.6.2', '<'):
        $content[] = '<pre>Comments versions prior to 0.6.2 are not supported for updating.
Please download 0.6.3.</pre>';
        break;

    case version_compare($currentVersion, '0.6.3', '<'):
        $content[] = '<pre>';
        $files = array('templates/alt_view.tpl', 'templates/view.tpl');
        if (PHPWS_Boost::updateFiles($files, 'comments')) {
            $content[] = '---The following templates copied locally.';
        } else {
            $content[] = '---The following templates failed to copy locally.';
        }
        $content[] = '    ' . implode("\n    ", $files);
        $content[] = '
0.6.3 Changes
-------------
+ Added setAnchor to comments.
+ Changed anchor tag to conform with Safari.
</pre>';


    case version_compare($currentVersion, '0.6.4', '<'):
        $content[] = '<pre>';
        $files = array('templates/settings_form.tpl', 'templates/recent.tpl');
        if (PHPWS_Boost::updateFiles($files, 'comments')) {
            $content[] = '---The following templates copied locally.';
        } else {
            $content[] = '---The following templates failed to copy locally.';
        }
        $content[] = '    ' . implode("\n    ", $files);
        $content[] = '
0.6.4 Changes
-------------
+ RFE #1720589 - Added ability to show most recent comments in a popup.
+ Added permission check on single comment view.
</pre>';

    case version_compare($currentVersion, '1.0.0', '<'):
        $content[] = '<pre>';
        $db = new PHPWS_DB('comments_users');
        if (PHPWS_Error::logIfError($db->createTableIndex('user_id', null, true))) {
            $content[] = 'Warning: A problems occurred when trying to create a unique index on the comments_users table.';
        }

        $files = array('javascript/report/head.js', 'javascript/report/default.php', 'javascript/admin/head.js',
                       'templates/alt_view.tpl', 'templates/alt_view_one.tpl', 'templates/view.tpl',
                       'templates/view_one.tpl', 'templates/punish_pop.tpl', 'templates/reported.tpl',
                       'templates/style.css', 'img/lock.png');

        commentsUpdatefiles($files, $content);
        PHPWS_Boost::registerMyModule('comments', 'controlpanel', $content);
        PHPWS_Boost::registerMyModule('comments', 'users', $content);

        if (!PHPWS_Boost::inBranch()) {
            $content[] = file_get_contents(PHPWS_SOURCE_DIR . 'mod/comments/boost/changes/1_0_0.txt');
        }
        $content[] = '</pre>';

    case version_compare($currentVersion, '1.0.1', '<'):
        $content[] = '<pre>';
        $db = new PHPWS_DB('comments_items');
        $result = $db->addTableColumn('reported', 'smallint NOT NULL default 0');
        if (PHPWS_Error::logIfError($result)) {
            $content[] = 'Unable to create reported column on comments_items table.</pre>';
            return false;
        } else {
            $content[] = 'Table column added.';
        }
        $content[] = '1.0.1 Changes
-------------
+ Fixed missing reported column on comments_items table.</pre>';

    case version_compare($currentVersion, '1.1.0', '<'):
        $content[] = '<pre>';
        $db = new PHPWS_DB('comments_threads');
        $result = $db->addTableColumn('approval', 'smallint NOT NULL default 0');
        if (PHPWS_Error::logIfError($result)) {
            $content[] = 'Unable to create approval column on comments_threads table.</pre>';
            return false;
        } else {
            $content[] = 'Table column added to comments_threads.';
        }

        $db = new PHPWS_DB('comments_items');
        $result = $db->addTableColumn('approved', 'smallint NOT NULL default 1');
        if (PHPWS_Error::logIfError($result)) {
            $content[] = 'Unable to create approved column on comments_items table.</pre>';
            return false;
        } else {
            $content[] = 'Table column added to comments_items.';
        }

        $files = array('img/cancel.png', 'img/noentry.png', 'img/ok.png', 'templates/approval.tpl',
                       'templates/settings_form.tpl', 'javascript/quick_view/head.js',
                       'javascript/admin/head.js', 'templates/reported.tpl', 'templates/user_settings.tpl');
        commentsUpdateFiles($files, $content);

        $content[] = '1.1.0 Changes
-------------
+ Comments can be approved before posting.
+ Counter added to reported tab.
+ Added TinyMCE bbcode wysiwyg to comments.
+ Comment users removed when user deleted.
+ Better clean-up process when a user is removed.
+ User can set order preference.
+ Avatar graphic code tightened up.
</pre>';

    case version_compare($currentVersion, '1.2.0', '<'):
        PHPWS_Core::initModClass('demographics', 'Demographics.php');
        Demographics::registerField('avatar_id', array('type'=>'integer'));
        $content[] = 'Created "avatar_id" column in user demographics table.';

        Demographics::registerField('location', array('limit'=>'50'));
        $content[] = 'Created "location" column in demographics table.';

        $db = new PHPWS_DB('comments_threads');
        $sql = 'CREATE TABLE comments_monitors (
    thread_id   int NOT NULL,
    user_id     int NOT NULL,
    send_notice smallint NOT NULL default 1,
    suspended smallint NOT NULL default 0
);
CREATE INDEX comments_monitors_user_id_idx ON comments_monitors (user_id, thread_id);
CREATE INDEX comments_monitors_thread_id_idx ON comments_monitors (thread_id, send_notice);
';
        $result = $db->import($sql,true);
        if (PHPWS_Error::logIfError($result)) {
            $content[] = 'Unable to add "comments_monitors" table.</pre>';
            return false;
        }
        $content[] = 'Created "comments_monitors" table.';

        $db = new PHPWS_DB('comments_items');
        $result = $db->addTableColumn('anon_name', 'varchar(50) default NULL');
        if (PHPWS_Error::logIfError($result)) {
            $content[] = 'Unable to add "anon_name" column to comments_items table.</pre>';
            return false;
        }
        $content[] = 'Created "parent_author_id" column in comments_items table.';

        $db = new PHPWS_DB('comments_items');
        $result = $db->addTableColumn('parent_author_id', 'smallint NOT NULL default 0');
        if (PHPWS_Error::logIfError($result)) {
            $content[] = 'Unable to add "parent_author_id" column to comments_items table.</pre>';
            return false;
        }
        $content[] = 'Created "parent_author_id" column in comments_items table.';

        $db = new PHPWS_DB('comments_items');
        $result = $db->addTableColumn('parent_anon_name', 'varchar(50) default NULL');
        if (PHPWS_Error::logIfError($result)) {
            $content[] = 'Unable to add "parent_anon_name" column to comments_items table.</pre>';
            return false;
        }
        $content[] = 'Created "parent_anon_name" column in comments_items table.';

        $db = new PHPWS_DB('comments_items');
        $result = $db->addTableColumn('protected', 'smallint NOT NULL default 0');
        if (PHPWS_Error::logIfError($result)) {
            $content[] = 'Unable to add "protected" column to comments_items table.</pre>';
            return false;
        }
        $content[] = 'Created "protected" column in comments_items table.';

        $db = new PHPWS_DB('comments_threads');
        $result = $db->addTableColumn('locked', 'smallint NOT NULL default 0');
        if (PHPWS_Error::logIfError($result)) {
            $content[] = 'Unable to add "locked" column to comments_threads table.</pre>';
            return false;
        }
        $content[] = 'Created "locked" column in comments_threads table.';

        $db = new PHPWS_DB('comments_users');
        $result = $db->addTableColumn('suspendmonitors', 'smallint NOT NULL default 0');
        if (PHPWS_Error::logIfError($result)) {
            $content[] = 'Unable to add "suspendmonitors" column to comments_users table.</pre>';
            return false;
        }
        $content[] = 'Created "suspendmonitors" column in comments_users table.';

        $result = $db->addTableColumn('monitordefault', 'smallint NOT NULL default 1');
        if (PHPWS_Error::logIfError($result)) {
            $content[] = 'Unable to add "monitordefault" column to comments_users table.</pre>';
            return false;
        }
        $content[] = 'Created "monitordefault" column in comments_users table.';

        $result = $db->addTableColumn('securitylevel', 'smallint NOT NULL default -1');
        if (PHPWS_Error::logIfError($result)) {
            $content[] = 'Unable to add "securitylevel" column to comments_users table.</pre>';
            return false;
        }
        $content[] = 'Created "securitylevel" column in comments_users table.';

        $result = $db->addTableColumn('groups', 'varchar(50) NOT NULL');
        if (PHPWS_Error::logIfError($result)) {
            $content[] = 'Unable to add "groups" column to comments_users table.</pre>';
            return false;
        }
        $content[] = 'Created "groups" column in comments_users table.';

        PHPWS_Settings::load('comments');
        PHPWS_Settings::save('comments');
        PHPWS_Settings::reset('comments', 'email_subject');
        PHPWS_Settings::reset('comments', 'email_text');
        $content[] = 'Added new module settings.';

        $files = array('templates/', 'img/');
        if (PHPWS_Boost::updateFiles($files, 'comments'))
            $content[] = 'Updated the following files:';
        else
            $content[] = 'Unable to update the following files:';
        $content[] = "     " . implode("\n     ", $files);

        $content[] = '<pre>
1.2.0 Changes
-------------
+ Added database table: comments_monitors.
+ Added User demographics: suspendmonitors, monitordefault, securitylevel, and groups.
+ Added module settings: email_text, monitor_posts, allow_user_monitors.
+ Updated template files.
+ Fixed "My Page" settings entries.
+ Avatar management now works.
+ Added user ranking system.
+ Added thread monitoring.
+ Added a few image links.
+ Created a template image directory.  Images can now be customized per theme.
+ Added comment selection for bulk operations.
+ Changed default avatar size to 80x80 pixels.
+ New permission setting to allow admins to ban users from posting
+ Added data caching columns parent_author_id and parent_anon_name comments_items table
</pre>';

    }

    return true;
}


function commentsUpdateFiles($files, &$content)
{
    if (PHPWS_Boost::updateFiles($files, 'comments')) {
        $content[] = '--- Updated the following files:';
    } else {
        $content[] = '--- Unable to update the following files:';
    }
    $content[] = "     " . implode("\n     ", $files);
}


?>