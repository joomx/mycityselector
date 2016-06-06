JEXTER
======

Building
--------

```
# php ./jexter/build [ext_name]
```

"ext_name" is name of extension and same name of config file of project from "config" directory.

For example:

```
# php ./jexter/build mcs
```

for this example in directory "config" must be exists "mcs.json" file with project configuration.


Notes
-----

All lines with "@devnode" marker of project files will be remove from result package version
 
```
echo 'hello';
//@devnode need to add "world" to echo
// ^^^^ this line will be removed from result package of extension (total line!)
```

