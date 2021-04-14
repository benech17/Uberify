liste=[]
n=int(input("Entrez le nombre d'éléments de la liste: "))
i=1
while i<=n:
    a=int(input("Entrez l'élément {}: ".format(i)))
    liste.append(a)
    i=i+1
print("Voici la liste initiale: ",liste)
a=int(input("Entrez l'élément à supprimer: "))
i=0
while i<len(liste):
    if(liste[i]==a):
        liste.pop(i)
        i=i-1
    i=i+1
print("Voici la liste après suppression: ",liste)