const tablediv = document.getElementById('tables_div_content');
let tablescript = false
document.getElementById('tables_div').addEventListener('click', ()=>{
    if(tablediv.style.display === 'none'){
        const xhr = new XMLHttpRequest();
        xhr.open('POST', './classes/inc/reports_tables.inc.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onload = function(){
            if(this.status == 200){
                const text = JSON.parse(this.responseText);
                if(text['return']){
                    tablediv.innerHTML = text['return'];
                    tablediv.style.display = 'block';
                }
            }
        }
        xhr.send();
    } else if(tablediv.style.display === 'block'){
        tablediv.innerHTML = '';
        tablediv.style.display = 'none';
    }
    if(tablescript === false){
        const script = document.createElement('script');
        script.src = './classes/js/reports_tables.js'
        document.body.appendChild(script);
        tablescript = true;
    }
});
const chartsdiv = document.getElementById('charts_div_content');
let chartsscript = false;
document.getElementById('charts_div').addEventListener('click', ()=>{
    if(chartsdiv.style.display === 'none'){
        const xhr = new XMLHttpRequest();
        xhr.open('POST', './classes/inc/reports_charts.inc.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onload = function(){
            if(this.status == 200){
                const text = JSON.parse(this.responseText);
                if(text['return']){
                    chartsdiv.innerHTML = text['return'];
                    chartsdiv.style.display = 'block';
                }
            }
        }
        xhr.send();
    } else if(chartsdiv.style.display === 'block'){
        chartsdiv.innerHTML = '';
        chartsdiv.style.display = 'none';
    }
    if(chartsscript === false){
        const script = document.createElement('script');
        script.src = './classes/js/reports_charts.js'
        document.body.appendChild(script);
        chartsscript = true;
        const link = document.createElement('link');
        link.rel = 'stylesheet';
        link.type = 'text/css';
        link.href = './classes/css/charts.css';
        document.body.appendChild(link);
    }
});
const progdiv = document.getElementById('progress_div_content');
let progscript = false;
document.getElementById('progress_div').addEventListener('click', ()=>{
    if(progdiv.style.display === 'none'){
        const xhr = new XMLHttpRequest();
        xhr.open('POST', './classes/inc/reports_progress.inc.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onload = function(){
            if(this.status == 200){
                const text = JSON.parse(this.responseText);
                if(text['return']){
                    progdiv.innerHTML = text['return'];
                    progdiv.style.display = 'block';
                }
            }
        }
        xhr.send();
    } else if(progdiv.style.display === 'block'){
        progdiv.innerHTML = '';
        progdiv.style.display = 'none';
    }
    if(progscript === false){
        const script = document.createElement('script');
        script.src = './classes/js/reports_progress.js'
        progdiv.appendChild(script);
        progscript = true;
    }
});
