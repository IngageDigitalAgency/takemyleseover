fldr="gadra"
echo "$fldr"
for i in $(find | grep admin/backend) ; do
    echo "$n = $i"
    let "n = $n + 1"
    if echo "$i" | grep -q "custom"; then
    echo "skipping ".$i
    else
    diff "$i" "../$fldr/$i"
    fi
done
for i in $(find | grep admin/classes) ; do
    echo "$n = $i"
    let "n = $n + 1"
    if echo "$i" | grep -q "custom"; then
    echo "skipping ".$i
    else
    diff "$i" "../$fldr/$i"
    fi
done
for i in $(find | grep admin/js) ; do
    echo "$n = $i"
    let "n = $n + 1"
    diff $i ../$fldr/$i
done
for i in $(find | grep admin/css) ; do
    echo "$n = $i"
    let "n = $n + 1"
    diff $i ../$fldr/$i
done
for i in $(ls -1 admin/*.php) ; do
    echo "$n = $i"
    let "n = $n + 1"
    if echo "$i" | grep -q "custom"; then
    echo "skipping ".$i
    else
    diff $i ../$fldr/$i
    fi
done
for i in $(find | grep admin/frontend/modules/) ; do
    echo "$n = $i"
    let "n = $n + 1"
    if echo "$i" | grep -q "custom"; then
    echo "skipping ".$i
    else
    diff $i ../$fldr/$i
    fi
done
for i in $(ls admin/frontend/forms/*.html) ; do
    echo "$n = $i"
    let "n = $n + 1"
    diff $i ../$fldr/$i
done

echo "index.php"
diff index.php ../$fldr/index.php
echo "admin/index.php"
diff admin/index.php ../$fldr/admin/index.php
echo "admin/render.php"
diff admin/render.php ../$fldr/admin/render.php
echo "admin/frontend/Frontend.php"
diff admin/frontend/Frontend.php ../$fldr/admin/frontend/Frontend.php
echo "admin/backend/Backend.php"
diff admin/backend/Backend.php ../$fldr/admin/backend/Backend.php
