# sonic 插件介绍

> sonic 是一款 loading 效果插件，使用 canvas 自动生成 loading 图片。官方提供loading图片实时预览且支持 .gif/.png 格式下载。
> [官方演示网址](http://james.padolsey.com/p/Sonic/repo/demo/demo.html)
> [GitHub 源码](https://github.com/padolsey/sonic.js)
> [loading 图片下载和预览](http://padolsey.github.io/sonic-creator/)

## 使用方法

- 插件使用入门 **[Demo1](https://github.com/oldinaction/Git/blob/master/jQuery-Plugin/loading/sonic.js/demo1.html)**
- 项目中使用 **[Demo2](https://github.com/oldinaction/Git/blob/master/jQuery-Plugin/loading/sonic.js/demo2.html)**

  项目中使用原理：现将页面的主要内容隐藏，将body的背景设成下载的gif图片(否则显示跳出背景，然后显示loading，有闪屏的现象)，然后显示loading(loading图片的位置要和背景图片的位置重合)，当页面加载完后，显示页面主要内容。

## 插件评价

提供的 .gif/.png 图片下载，不支持透明背景
不支持遮罩层
