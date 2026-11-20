# Cost Centers for iTop

[![GPL-3.0 license](https://img.shields.io/github/license/rudnerbjoern/iTop-br-costcenter)](LICENSE)
[Download from GitHub Releases](https://github.com/rudnerbjoern/iTop-br-costcenter/releases)

Create an organization-specific cost center catalog in iTop and assign its entries
to configuration items (CIs), persons, and teams. Open a cost center to see the
CIs assigned to it and the child cost centers below it.

## What you can do

- **Build a cost center hierarchy.** Give each cost center a code, a name, and an
  owner organization. Add a parent when it belongs under another cost center.
  Codes are unique within each organization.
- **Find CIs by cost center.** Assign cost centers on supported CI forms and filter
  searches by cost center. Available choices follow the organization hierarchy of
  the CI.
- **Assign cost centers to contacts.** Set the **Cost center** field on person
  and team forms. The field is also available in contact, person, and team searches.
- **Track active and inactive entries.** New cost centers are active by default.
  Set a cost center to inactive when it is retired.

## Compatibility

| iTop version   | Which version to use                              |
| -------------- | ------------------------------------------------- |
| 3.3.0 or later | Version 3.3.0                                     |
| 3.2.x          | The [itop/3.2 branch][itop-32] or a 3.2.x release |
| 2.7 or 3.1     | The [itop/2.7 branch][itop-27]                    |

The base module uses iTop Configuration Management. The optional integrations
also require their corresponding iTop module or extension.

## Install and get started

1. Download the ZIP for your iTop version from
   [GitHub Releases](https://github.com/rudnerbjoern/iTop-br-costcenter/releases).
2. Extract the package into the `extensions/` directory of your iTop installation.
   The descriptor should be at `extensions/br-costcenter/extension.xml`.
3. Run iTop Setup and select **Datamodel: Cost Center**.
4. In the iTop back office, open **Catalogs → Cost Centers**. Create a cost center,
   then assign it using the **Cost center** field on a supported CI form.

The interface labels are available in English and German.

## Optional integrations

The bundle includes five bridges. iTop Setup uses the installed modules and
their dependencies to determine which bridges apply.

- **iTop Tickets Management** adds cost center fields to tickets.
- **ITIL User Request Management** exposes the field on user request forms and
  makes it read-only in the resolved state.
- **Project Management Light** exposes the field on projects.
- **Medical Devices** exposes the field on supported medical device forms.
- **Order Requests** supports a default cost center per request type. The default
  fills a blank field or applies when the request type changes.

## Development and license

See the [test instructions](tests/README.md) for local validation and the
[release instructions](scripts/README.md) for packaging.

Licensed under the [GNU General Public License v3.0](LICENSE).
Copyright (c) 2024-2026 Björn Rudner.

[itop-32]: https://github.com/rudnerbjoern/iTop-br-costcenter/tree/itop/3.2
[itop-27]: https://github.com/rudnerbjoern/iTop-br-costcenter/tree/itop/2.7
