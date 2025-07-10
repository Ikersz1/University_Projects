import {
    v4 as uuidv4,
    //validate as uuidValidate
} from 'uuid';

class BookId {
    private readonly value: string;

    public constructor(id: string) {
        //this.guard(id);

        this.value = id;
    }

    public static random(): BookId {
        return new BookId(uuidv4());
    }

    // private guard(id: string): void {
    //    if (false === uuidValidate(id)) {
    //        throw Error(`Invalid Book ID ${id}`);
    //    }
    // }

    public getValue(): string {
        return this.value;
    }

    public equals(other: BookId): boolean {
        return this.value === other.value;
    }
}

export default BookId;
