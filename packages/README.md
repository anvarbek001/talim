# packages/

Locally-vendored, trimmed-down copies of third-party packages that can't be
installed normally on our production host.

## google/apiclient-services

The real upstream package (https://github.com/googleapis/google-api-php-client-services)
has ~37,000 files (one class per resource, across all 200+ Google APIs — Gmail,
Drive, Calendar, Sheets, etc.), even though `App\Services\YoutubeUploadService`
only ever uses `Google\Service\YouTube`. Our production hosting (Ahost) has a
50,000-inode account quota, and even the temporary extraction of the full zip
during `composer install` blows past it — well before the `google/apiclient`
package's own `Google\Task\Composer::cleanup` post-install trimming step (see
`extra.google/apiclient-services` in the root `composer.json`) ever gets a
chance to run.

This directory holds a hand-trimmed copy — just `composer.json`, `autoload.php`,
and `src/YouTube.php` + `src/YouTube/` — wired in via a Composer `path`
repository (see `repositories` in the root `composer.json`), so `composer
install` copies these ~250 files directly instead of downloading and
extracting the full upstream package.

**If YoutubeUploadService (or anything else) starts using a different
`Google\Service\*` class**, copy that service's file(s) from
`vendor/google/apiclient-services/src/` (after a normal, non-quota-constrained
`composer install`) into `src/` here.

**To pick up a newer apiclient-services release**, bump the `version` in
`composer.json` here and re-copy the `YouTube.php` / `YouTube/` files from a
fresh full install.
