liste=[]
n=int(input("Entrez le nombre d'éléments de la liste: "))
for i in range(0,n):
    a=int(input("Entrez l'élément {}: ".format(i+1)))
    liste.append(a)
print("Voici la liste: ",liste)
max=liste[0]
for a in liste:
    if a>max:
        max=a
print("Le max est: ",max)