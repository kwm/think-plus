#!/usr/bin/env sh

vendorPath=$(readlink -f "$(dirname "$0")/../")

patch -N -p1 -d $vendorPath/../thinkphp < $vendorPath/kwm/think-plus/patch/think-plus.patch
find $vendorPath/../thinkphp/ -name '*.rej' | xargs rm -f