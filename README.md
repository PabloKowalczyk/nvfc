## NVFC - NVidiaFanController

Written in PHP, fan speed controller for Nvidia graphic cards. 

### Requirements

- PHP 7.2+
- Linux OS
- `nvidia-setting` binary, from Nvidia Linux drivers

### Installation

```bash
composer global require pablok/nvfc
```

### Example usage

```bash
nvfc watch --start-fan=25 --start-temp=30 --end-temp=90 --end-fan=100 --interval=5
```
