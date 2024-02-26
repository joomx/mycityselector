JEXTER
======

NOTE: это была попытка сделать универсальный сборщик пакетов из исходного кода. Но на него все время не хватало времени и в итоге он был заброшен на уровне, достаточном для сборки текущего расширения.

Сборка пакета производится командой ```php ./jexter/build mcs```

В процессе каждой сборки создается копия всех собранных файлов в директории ```./jexter/src_copy/*```
А собранный пакет сохраняется в файл ```jexter/extensions/mcs/pkg_mycityselector.zip```

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

for this example in directory "config" must exist "mcs.json" file with project configuration.


Notes
-----

All lines with "@devnode" marker of project files will be remove from result package version
 
```
echo 'hello';
//@devnode need to add "world" to echo
// ^^^^ this line will be removed from result package of extension (total line!)
```

