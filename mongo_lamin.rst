语言包管理系统 lamin 数据结构
===============================

所有语言版本 la_langs 表
-------------------------------

::

    _id                     MongoId                 id
    key                     string                  语言的key，如cs、ct、en、my、id等
    value                   string                  语言的中文名称，如简体中文、英语、马来语等

所有标签列表 la_tags 表
------------------------------

::

    _id                     MongoId                 id
    key                     string                  标签名称，跟变量一样命名
    value                   string                  标签中文显示的名称

所有语言列表 la_items 表
-----------------------------

::

    _id                     MongoId                 id
    la_id                   int                     唯一的一个语言id，从1开始递增
    key                     string                  语言的key值，用于代码里面调用，原则上该值为 "la_" + la_id，如la_1004
    cs                      string                  表示中文版本的语言内容，这里的键值从 la_langs 里面取
    tags                    string[]                标签数组，元素为 la_tags 里面的key值
