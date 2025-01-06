
# Shopware-Übung: Ein einfaches Plugin erstellen

## Ziel
Erstelle ein simples Shopware 6-Plugin, das über die Store API eine Liste von Produkten abruft und diese in einer angepassten Ausgabe darstellt. Ziel ist es, den grundlegenden Umgang mit dem Framework, APIs und Context zu lernen.

---

## Voraussetzungen
- **Kenntnisse:** PHP und Symfony (z.B. Services, Dependency Injection)
- **Umgebung:** Installierte Shopware 6 Entwicklungsumgebung

---

## Aufgabenstellung

### 1. Vorbereitung & Plugin-Grundstruktur
- Lege im `custom/plugins/`-Verzeichnis ein neues Plugin an (z.B. `MyProductListPlugin`).
- Erstelle die erforderlichen Basisdateien:
    - `composer.json`
    - `MyProductListPlugin.php`
    - `Resources/config/services.xml`
- Aktiviere und installiere das Plugin:
  ```bash
  bin/console plugin:refresh
  bin/console plugin:install --activate MyProductListPlugin
  ```

### 2. Service zur Produktabfrage
- Definiere einen Symfony-Service (z.B. `MyProductListService`), der über die Store API eine Produktliste abruft.
- Verwende den `SalesChannelContext`, um sicherzustellen, dass du Daten für den aktiven Verkaufskanal erhältst.
- Nutze den Store API-Endpunkt `/store-api/product`, um Produkt-Titel und Preise abzufragen.

### 3. Einfaches Command zur Ausgabe
- Erstelle einen Symfony-Console-Command (z.B. `bin/console my-product-list:show`), um die Produktliste auszugeben.
- Greife im Command auf deinen Service zu.
- Führe den Command aus, um sicherzustellen, dass die Produktdaten korrekt abgerufen werden.

### 4. Context korrekt nutzen
- Implementiere die Nutzung des `SalesChannelContext`, um unterschiedliche Verkaufskanäle und deren spezifische Daten (z.B. Währung) zu unterstützen.

### 5. Ergebnis überprüfen
- Teste dein Plugin lokal und überprüfe die Konsolenausgabe auf korrekte Daten.

---

## Erweiterung (Optional)
- Füge Filter- oder Sortier-Parameter hinzu (z.B. nur Produkte einer bestimmten Kategorie).
- Experimentiere mit unterschiedlichen Contexts für verschiedene Verkaufskanäle.

---

## Referenzen
- [Shopware 6 Developer Docs](https://developer.shopware.com/)
- [Store API Dokumentation](https://developer.shopware.com/docs/concepts/api/store-api)
- [Symfony Services](https://symfony.com/doc/current/service_container.html)

---

## Endergebnis
Durch diese Übung wirst du ein funktionales Plugin erstellen, das über die Store API Produktdaten abruft und diese in der Konsole ausgibt. Gleichzeitig erlangst du ein besseres Verständnis für die Shopware-Architektur, Bundles, Services und die Verwendung von Contexts.