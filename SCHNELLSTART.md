# Schnellstart

Auf dem Server per SSH, im WordPress-Verzeichnis. Voraussetzungen: PHP 8.4+, WordPress 6.7+, `git`, `perl`.

## Schritte

```bash
cd wp-content/themes/
git clone https://github.com/able2create/underscores-a2c-modern.git _s
cd _s
./setup.sh sundp "a2c - Theme" "Martin" "https://able2create.com"
cd .. && mv _s sundp
wp theme activate sundp
```

`setup.sh` fragt zweimal nach (weiterfahren, Entwicklungsdateien löschen). Mit `-y` als fünftem Argument werden beide Fragen mit Ja beantwortet. Das Aufräumen entfernt `.git`, `.github`, die Dokumentation, `composer.json`, `phpcs.xml.dist` und die Install-Skripte.

Alles in einem Befehl:

```bash
cd wp-content/themes/ && \
git clone https://github.com/able2create/underscores-a2c-modern.git _s && \
cd _s && \
./setup.sh sundp "a2c - Theme" "Martin" "https://able2create.com" -y && \
cd .. && mv _s sundp && \
wp theme activate sundp
```

## Was das Skript ersetzt

- `'_s'` wird zur Text Domain `sundp`
- `_s_` wird zum Funktionspräfix `sundp_` (auch bei den Filtern, z. B. `sundp_features`)
- `_s-` wird zu `sundp-` (Handles), `_S_` zu `SUNDP_` (Konstanten)
- der `style.css`-Header (Theme Name, Author, Author URI, Text Domain)
- `languages/_s.pot` wird zu `languages/sundp.pot`

Ausführliche Anleitung, auch für die manuelle Installation: [INSTALL.md](INSTALL.md) (nur ohne Aufräumen vorhanden).
