# Veröffentlichung auf GitHub

Diese Repository-Version ist so vorbereitet, dass sie direkt in ein neues GitHub-Repository übertragen werden kann.

## 1. Neues Repository anlegen

Empfohlener Repository-Name:

```text
mailhilfe-order-note-manager
```

Beim Anlegen auf GitHub keine zusätzliche README, `.gitignore` oder Lizenz erzeugen, da diese Dateien bereits enthalten sind.

## 2. Dateien übertragen

Im entpackten Repository-Ordner:

```bash
git init
git branch -M main
git add .
git commit -m "Release 2.0.6"
git remote add origin https://github.com/DEIN-BENUTZERNAME/mailhilfe-order-note-manager.git
git push -u origin main
```

## 3. Release-ZIP bauen

```bash
bash scripts/build-release.sh
```

Unter Windows PowerShell:

```powershell
./scripts/build-release.ps1
```

Anschließend liegt die installierbare Datei unter:

```text
build/mailhilfe-order-note-manager-2.0.6.zip
```

## 4. GitHub-Release anlegen

1. Auf GitHub **Releases → Draft a new release** öffnen.
2. Tag `2.0.6` anlegen.
3. Titel `Mailhilfe Order Note Manager 2.0.6` verwenden.
4. Die Release-ZIP aus dem Ordner `build` hochladen.
5. Die wichtigsten Änderungen aus `CHANGELOG.md` übernehmen.

## 5. Screenshots und Grafiken

Die GitHub-Dokumentation verwendet Dateien aus:

```text
docs/screenshots/
docs/assets/
```

Für WordPress.org gehören Screenshots, Banner und Icons separat in den SVN-Ordner `assets`. Sie dürfen nicht in die installierbare Plugin-ZIP übernommen werden. Der Release-Build schließt diese Dateien deshalb aus.

## 6. Automatische Prüfung

Der Workflow `.github/workflows/quality.yml` prüft bei Pushes und Pull Requests:

- PHP-Syntax
- JavaScript-Syntax
- Release-Build
- ZIP-Integrität
- Ausschluss typischer Entwicklungsdateien aus dem Release
