# echarts-world-features
echarts world features

## build world map js file
```bash
php -d memory_limit=1000M echarts.php
```
## echarts series mapType
```js
series: [
    {
        name: 'devices',
        type: 'map',
        mapType: 'world-features',
        roam: true,
        itemStyle:{
            emphasis:{label:{show:true}}
        },
        data: data
    }
]
```
