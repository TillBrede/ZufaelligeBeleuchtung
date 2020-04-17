# Zufaellige Beleuchtung
Das Modul ermöglicht es den Farbwert von Lampen zufällig zwischen verschiedenen Farben zu schalten.

### Inhaltsverzeichnis

1. [Funktionsumfang](#1-funktionsumfang)
2. [Voraussetzungen](#2-voraussetzungen)
3. [Software-Installation](#3-software-installation)
4. [Einrichten der Instanzen in IP-Symcon](#4-einrichten-der-instanzen-in-ip-symcon)
5. [Statusvariablen und Profile](#5-statusvariablen-und-profile)
6. [WebFront](#6-webfront)
7. [PHP-Befehlsreferenz](#7-php-befehlsreferenz)

### 1. Funktionsumfang

* Farbvariablen können in einer Liste hinzugefügt werden
* Eine Liste mit Farben aus denen gewählt wird, welche erweitert werden kann
* Wenn das Modul deaktiviert wird, werden die ausgewählten Farbvariablen auf den Wert vor der Aktivierung gesetzt 
* Einstellung des Intervalls in dem die Farben gewechselt werden sollen

### 2. Vorraussetzungen

- IP-Symcon ab Version 5.0

### 3. Software-Installation

* Über den Module Store das 'Zufällige Beleuchtung'-Modul installieren.
* Alternativ über das Module Control folgende URL hinzufügen `https://github.com/symcon/ZufaelligeBeleuchtung`

### 4. Einrichten der Instanzen in IP-Symcon

 Unter 'Instanz hinzufügen' kann das 'Zufällige Beleuchtung'-Modul mithilfe des Schnellfilters gefunden werden.
    - Weitere Informationen zum Hinzufügen von Instanzen in der [Dokumentation der Instanzen](https://www.symcon.de/service/dokumentation/konzepte/instanzen/#Instanz_hinzufügen)

__Konfigurationsseite__:

Name                    | Beschreibung
----------------------- | ------------------
Farbvariablen           | Liste mit Farbvariablen, welche geschaltet werden sollen
Farben                  | Farben von denen eine zufällig zum Schalten ausgewählt wird
Änderungsintervall      | Abstand zwischen den einzelenen Schaltungen in Sekunden
Gleichzeitiges Schalten | legt fest ob alle Farbvariablen auf die gleiche Farbe gesetzt werden oder auf verschiedene

### 5. Statusvariablen und Profile

Die Statusvariablen/Kategorien werden automatisch angelegt. Das Löschen einzelner kann zu Fehlfunktionen führen.

#### Statusvariablen

Name  | Typ     | Beschreibung
----- | ------- | ----------------
Aktiv | Boolean | Schaltet das Modul an oder aus

#### Profile

Es werden keine weiteren Profile erstellt.


### 6. WebFront

Das Modul kann hier De-/Aktiviert werden.

### 7. PHP-Befehlsreferenze

`boolean ZB_ChangeLight(integer $InstanzID);`
Wählt zufällige Farben aus und setzt die ausgewählten Variablen auf diese

Beispiel:
`ZB_ChangeLight(12345);`
