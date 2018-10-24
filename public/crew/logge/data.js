
//Check dupes
var dupes = {}
var data1 = []
for (i = 0; i < data.length; i++) {
  if (!dupes[data[i].title]) {
    dupes[data[i].title] = true
    data1.push(data[i])
  }
}

data1.sort((a,b) => b.stars - a.stars)

var str = ""
for (i=0;i<data1.length;i++) {
  var a = data1[i]
  str += `
<tr>
  <td><a href="${a.href}">${a.title}</a></td>
  <td><img src="${a.img}"></td>
  <td>${a.votes}</td>
  <td>${a.stars}</td>
</tr>`
}

document.getElementById("tableBody").innerHTML = str;