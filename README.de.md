<p align="center">
  <img src="docs/assets/banner-1544x500.png" alt="Mailhilfe Order Note Manager für WooCommerce" width="100%">
</p>

# Mailhilfe Order Note Manager für WooCommerce

Mehrsprachige Vorlagen für WooCommerce-Bestellnotizen mit Bedingungen, Platzhaltern, bearbeitbarer Vorschau, Verlauf, Rollenrechten und HPOS-Kompatibilität.

## Funktionsumfang

- Vorlagen erstellen, bearbeiten, duplizieren, kategorisieren, favorisieren und sortieren.
- Interne Notizen und Kundennotizen direkt in WooCommerce-Bestellungen hinzufügen.
- Bearbeitbare Vorschau mit echten Bestell- und Kundendaten.
- Bedingungen nach Bestellstatus, Zahlungsart, Versandart, Rechnungsland und Bestellwert.
- Vorlagensprache und sprachabhängige Auswahl in mehrsprachigen Shops.
- Persönliche Favoriten und zuletzt verwendete Vorlagen pro Benutzer.
- Zentraler Verlauf für Notizen, Nutzung und E-Mail-Verarbeitung mit Link zur Bestellung.
- Testbestellung für die Vorschau im Vorlageneditor.
- JSON-Import und -Export mit Vorschau vor dem Import.
- Rollen- und Rechteverwaltung.
- Diagnose-Seite für WordPress, WooCommerce, HPOS, E-Mailstatus, Sprache und Cache.
- HPOS-kompatible WooCommerce-APIs und serverseitige Berechtigungsprüfungen.
- Integrierte Hilfe, FAQ und Sprachdateien für 20 häufig genutzte Sprachen sowie Deutsch formal.
- Hooks und Filter für eigene Erweiterungen.

## Installation

1. Eine installierbare Release-ZIP herunterladen.
2. In WordPress **Plugins → Installieren → Plugin hochladen** öffnen.
3. ZIP-Datei hochladen und aktivieren.
4. Den Menüpunkt **Mailhilfe Order Notes** öffnen.
5. Eine Vorlage erstellen oder Demo-Vorlagen installieren.
6. Eine WooCommerce-Bestellung öffnen und die Box **Bestellnotiz-Vorlage** verwenden.

## Release-ZIP erstellen

Unter Linux, macOS oder Git Bash:

```bash
bash scripts/build-release.sh
```

Unter Windows PowerShell:

```powershell
./scripts/build-release.ps1
```

Die installierbare ZIP wird im Ordner `build` erstellt. GitHub-Dokumente, Workflows, Screenshots und Entwicklungsdateien werden nicht in die Plugin-ZIP übernommen.

## Weitere Dokumentation

- [GitHub-Veröffentlichung](GITHUB-PUBLISHING.md)
- [Entwickler-Hooks und Filter](docs/DEVELOPER-HOOKS.md)
- [Übersetzungen](docs/TRANSLATIONS.md)
- [Release-Prüfliste](docs/RELEASE-CHECKLIST.md)
- [Sicherheitsrichtlinie](SECURITY.md)
- [Mitwirken](CONTRIBUTING.md)

Die für WordPress.org vorgesehene Beschreibung sowie der vollständige Änderungsverlauf befinden sich in [`readme.txt`](readme.txt).

## Lizenz

GPL-2.0-or-later. Siehe [LICENSE](LICENSE).
