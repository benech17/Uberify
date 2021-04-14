liste = []
n = int(input("Entrez le nombre d'éléments de la liste: "))
for i in range(0, n):
    a = int(input("Entrez l'élément {}: ".format(i+1)))
    liste.append(a)
print("La liste avant tri: ", liste)
liste.sort()
print("La liste après tri: ", liste)
a = int(input("Entrez l'élément à insérer: "))
i = 0
while i:
    i = i+1
liste.insert(i, a)
print("La liste après insertion: ", liste)
