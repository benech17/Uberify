mots=[]
n=int(input("Entrez le nombre de mots de la liste: "))
for i in range(0,n):
    mot=input("Entrez le mot {}: ".format(i+1))
    mots.append(mot)
print("Voici la liste avant tri: ",mots)
mots.sort()
print("Voici la liste après tri: ",mots)