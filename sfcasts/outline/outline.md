# Going Global: Translate your Site

## Installing the Translation Component

- Internationalization (i18n) vs Localization (l10n)
- Site tour
- `composer require translation`
- Explore files
  - `translations/`: where translations are stored
  - `config/packages/translation.yaml`: configuration
- Locale codes
  - `fr_FR` vs `fr`
- Add `enabled_locales: ['en', 'fr', 'es']` to `config/packages/translation.yaml`
  - Slight performance increase (don't cache unneeded locales)
  - More important: get a list of available locales in your app
    - Language switcher
    - Bulk translation tasks
- in `base.html.twig`, add `<html lang="{{ app.locale }}">`
  - inspect source

## "Localized" Routes

- Different methods for determining the locale
  - User setting - in the db
  - Request header (`Request::getLanguages()`)
  - URLs
    - translate the entire path: `/about` & `/a-propos`
    - subdomain: `en.example.com/about` & `fr.example.com/about`
    - prefix the path: `/en/about` & `/fr/about`
- `ArticleController::index()` - add `['en' => '/en', 'fr' => '/fr']` to the `#[Route]` attribute
  - demo - click on an article - no more prefix...
  - Duplicate for every route? :gross:
  - Revert change
- `config/routes.yaml`
  - add `prefix`: `en: /en`, `fr: /fr,`, `es: /es`
  - demo - click on an article - locale prefix "sticks"
- `symfony console debug:router`
  - routes duplicated/suffixed for each locale
  - unsuffixed version is "magic" - uses the current locale
- unprefixed default
  - go to `/`: not found
  - `config/routes.yaml`: `en: ''`
  - go to `/`: works - default locale
  - `/fr`: still works

## Create a Language Switcher

- `_locale` route parameter
  - "special" route parameter - not added as a query parameter
  - show in profiler
- `base.html.twig`: add language switcher code
  - `app.locale`
  - `for locale in app.enabled_locales`
  - `if locale != app.locale`
  - `{{ locale }}`
  - `path(app.current_route, app.current_route_parameters|merge({_locale: locale}))`
  - Demo! Both homepage and article page
- Homepage trailing slash: `/es` => `/es/`
    - `config/routes.yaml`: `trailing_slash_on_root: false`

## Translating Content

- `ArticleController::index()`
  - Inject `TranslatorInterface $translator`
  - `dump($translator->trans('Hello world'))`
  - Demo, switch locale, same text...? Symfony isn't that smart... yet!
- Create `translations/messages.fr.yaml`
  - `"Hello World!": "Bonjour le monde!"`
  - refresh - see the translation
  - choose another locale - fallback to English
- Twig:
  - in `base.html.twig`, add `{{ 'Hello world!'|trans }}`
- `TranslatorInterface::trans()`
  - id: the string to translate
  - parameters: optional context (replacement values)
  - domain: "category" of translations (default: `messages`)
      - bundles/components can provide their own (ie `validator`)
  - locale: translate to? (default: current locale)
- Translations loader
  - `messages.fr.yaml`:
    - `messages`: domain
    - `fr`: locale
    - `yaml`: format - easiest for developers
- Cleanup `ArticleController::index()`

## Translation "Keys"

- Problem with translating raw content
    - In `base.html.twig`, remove `!` from `Hello world!`
    - we broke all other translations `fr` - just because of punctuation...
- Translation keys:
    - change to `hello_world|trans`
    - ew, we even broke the English translation
    - add `translations/messages.en.yaml` & `hello_world: "Hello World!"`
    - update `messages.fr.yaml` to `hello_world: "Bonjour le monde!"`
- Cleanup
- Translate our site!
- `base.html.twig`:
    - Start with "Local Asteroids"
        - Copy text
        - "keys" can have separators - prefix with the template
          `{{ 'base.local_asteroids'|trans }}`
        - inside `messages.en.yaml`: `base`, `local_asteroids: "<paste>"`

## Placeholders and Pluralization

- `show.html.twig`: "x Comments"
    - `'article.show.comments'|trans({'%count%': comments|length})`
    - `messages.en.yaml`: `article.show.comments: "%count% Comments"`
    - What if we have 1 comment: "1 Comments"
    - `messages.en.yaml`: `article.show.comments: "1 Comment|%count% Comments"`
    - More complex placeholders and pluralization: https://symfony.com/doc/current/reference/formats/message_format.html

## HTML in Translations

- Jump to footer html...
  - Tricky but a translation value can contain HTML
  - Pase inside single quotes
  - Refresh... escaped HTML??
  - use `|raw`

## Translation Providers

- Can create translations manually, but it's a lot of work
- https://symfony.com/doc/current/translation.html#translation-providers
- Don't have a preference, using Crowdin because it's one that sponsors Symfony
- `composer require symfony/crowdin-translation-provider`
  - check `config/packages/translation.yaml` & `.env`
  - create `.env.local` and copy dsn
  - Remove `ORGANIZATION_DOMAIN.` (we're using a personal account)
- Create Crowdin account
  - Account Settings > API > Personal Access Token > New Token
    - Name, All credentials, Create & Copy, Paste in `.env.local` as `API_TOKEN`
  - Home > Create Project > "Space Bar"
    - Source language: English
    - Target languages: French, Spanish
  - On project page
    - find `ID`, copy to `.env.local` as `PROJECT_ID`
    - "Spanish" code is `es-ES` that doesn't match our app's `es` locale
    - Settings > Languages > Language Mapping
      - "Spanish", "locale", "es"
- `symfony console translation:push`
- Crowdin Project Dashboard -> "View Strings"
  - Go through translations, using AI to fill in and fix any errors
  - "Save"
- `symfony console translation:pull --domains=messages --format=yaml --as-tree=3`
  - Check `translations/messages.es.yaml` and `messages.fr.yaml`
