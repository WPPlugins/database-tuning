| Database Tuning
|
| Maintenance and tuning for the WordPress database
|
| WordPress plugin by Dietmar Rabich
|
| http://rabich.de/
|
| Stable tag: 0.6d
|
| $LastChangedDate: 2008-05-12 09:50:40 +0200 (Mon, 12 May 2008) $
| $LastChangedRevision: 45345 $

With this WordPress plugin maintenance work for the tables and
indexes of the WordPress database is possible.

Additionally some indexes can be added for a better reading
performance.

It is very recommended to make an backup of your database periodically
and before you do any major work on your data.

If you use the plugin you should know what you are doing. You have
to have the necessary database knowledge.

The plugin appends a new topic to the administration menu.

An explicit hint: the plugin is not a replacement of an
administration tool for an MySQL database.

Informations about WordPress at http://wordpress.org/ or
http://wordpress-deutschland.org/ .

More informations under liesmich.txt (in german language).


INSTALLATION

The plugin (database-tuning.php) has to be copied to the plugins
directory within the WordPress directory. Afterwards the plugin can
be activeded like all the other plugins.

If you are using an older version you have to drop the additionally
indexes and to recreate the WordPress indexes first. After the
plugin replacement you can create the new versions of the indexes.

If you update WordPress you should act in the same way.

(The reason for dropping and recreating is a possibly change of the
index structure. But there is no problem if you forgot to drop the
old indexes.)


LOCALIZATION

All text parts are written in german. However a localization is
possible since the plugin uses suitable functions. The necessary files
for translation encloded to the archive file. As an example a file for
use within the english language area in the U.S.A. available. You can
find more information about localization at http://wordpress.org/ .

If you like to use the plugin in english language you should copy the
file typographical-improvements-en_US.mo additionally to your plugins
directory and you have to set WPLANG to "en_US" in the wp-config.php
file.

If you like the english version but you are using another language
parameter in your wp-config.php file you can copy the file
typographical-improvements-en_US.mo and rename the copy to
typographical-improvements-<<WPLANG>>.mo where <<WPLANG>> is your
parameter from wp-config.php file.


LICENSE

Copyright (c) 2007 Dietmar Rabich, Dülmen, Deutschland

This program is free software; you can redistribute it and/or modify it
under the terms of the GNU General Public License (version 2) as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful, but
WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License along
with this program; if not, write to the Free Software Foundation, Inc.,
51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
