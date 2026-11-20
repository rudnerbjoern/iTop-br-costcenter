## iTop 3.3 compatibility

The 3.3.x series requires iTop 3.3.0 or later. For iTop 3.2, use a 3.2.x
release or the [iTop 3.2 maintenance branch](https://github.com/rudnerbjoern/iTop-br-costcenter/tree/itop/3.2).

With version 3.3.0, you can assign cost centers to persons and teams and use
the **Cost center** filter in contact, person, and team searches.

## 📦 Installation

1. Download the `br-costcenter-vX.Y.Z.zip` asset attached to this release.
2. Extract its contents into your iTop installation's `extensions/` directory,
   preserving the included folder structure. The package creates a single
   `extensions/br-costcenter/` folder containing the manifest and all six modules.
3. Run iTop setup and select **Datamodel: Cost Center** and the applicable bridge modules to activate the extension.

### Verify your download

Download the matching `.zip.sha256` file alongside the ZIP. From the folder
containing both files, verify the package before extracting it:

```sh
sha256sum --check br-costcenter-vX.Y.Z.zip.sha256
```

On Windows, use `Get-FileHash .\br-costcenter-vX.Y.Z.zip -Algorithm SHA256`
and compare the result with the hash in the `.sha256` file.

