rem  C:\Python\CS50Web Programming with Python and JavaScript\gitdir\PortableGit\git-cmd.exe

rem  cd ..\..\myrepo\OIT_Computers

git config --global user.email "tomokogun@gmail.com"
git config --global user.name "tomhtml5"

echo "# OIT_Computers" >> README.md
git init
git add README.md
git commit -m "first commit"
git branch -M master
git remote add origin https://github.com/tomhtml5/OIT_Computers.git
git push -u origin master