# Schnellstart Installation

## Auf dem Server (per SSH)

### Schritt 1: Theme klonen
```bash
cd wp-content/themes/
git clone https://github.com/able2create/underscores-a2c-modern.git _s
cd _s
```

### Schritt 2: Setup ausführen
```bash
./setup.sh sundp "a2c - Theme" "Martin" "https://able2create.com"
```

### Schritt 3: Verzeichnis umbenennen
```bash
cd ..
mv _s sundp
```

### Schritt 4: Theme aktivieren
```bash
wp theme activate sundp
```

## Das war's! 🎉

Die Theme ist jetzt installiert und aktiviert mit:
- Theme Name: **a2c - Theme**
- Theme Slug: **sundp**
- Author: **Martin**
- Author URI: **https://able2create.com**

Alle `_s` Platzhalter wurden durch `sundp` ersetzt.

## Alternative: Alles in einem Befehl

```bash
cd wp-content/themes/ && \
git clone https://github.com/able2create/underscores-a2c-modern.git _s && \
cd _s && \
./setup.sh sundp "a2c - Theme" "Martin" "https://able2create.com" && \
cd .. && \
mv _s sundp && \
wp theme activate sundp
```

## Was macht das setup.sh Script?

- Ersetzt `'_s'` → `'sundp'` (Text Domain)
- Ersetzt `_s_` → `sundp_` (Function Prefix)
- Ersetzt `_s-` → `sundp-` (Handles)
- Ersetzt `_S_` → `SUNDP_` (Constants)
- Aktualisiert style.css Header (Theme Name, Author, etc.)
- Benennt languages/_s.pot → languages/sundp.pot um

Alle Änderungen werden **direkt** in den Dateien vorgenommen - kein Kopieren nötig!
